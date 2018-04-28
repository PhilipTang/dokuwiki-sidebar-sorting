<?php

/**
 * 文本排序思路：
 * 1. 读取源文件，以数组元素存储每一行
 * 2. 把行号追加到行内容结尾，使用“分隔符”分隔
 * 3. 使子级前面追加父级内容（行开头为空格的就是子级），使用“分隔符”分隔
 * 4. 整体排序
 * 5. 遍历排序后结果，根据每一行记录的历史行号向源文件数组查找替换本行内容
 * 6. 把修改后的内容写入新文件
 *
 * 关于第3点：
 * 1. 当行开头无空格，清空“前缀数组”
 * 2. 当行开头有空格，上一行开头空格数小于自己的，把“上一行内容”追加到“前缀数组”，联合成字符串加在自己内容前面，产生新内容，放进“待排序数组”
 * 3. 当行开头有空格，上一行开头空格数等于自己的，保持“前缀数据”元素不变，联合成字符串加在自己内容前面，产生新内容，放进“待排序数组”
 * 4. 当行开头有空格，上一行开头空格数大于自己的，把“前缀数组”元素减一，联合成字符串加载自己内容前面，产生新内容，放进“待排序数组”
 * 5. 关键字，“前缀数组”，数组类型
 * 6. 关键字，“上一行内容”，字符串类型，收尾去除空格
 *
 * 分隔符为“//”。
 */

// 1
$con = file('sort.input');

print_r($con);

// 2
$newCon = [];
$separator = '//';

foreach($con as $no => $val) {
    $val = rtrim($val);
    $newCon[] = $val . $separator . $no;;
}

print_r($newCon);

// 3
$sortCon = [];
$prefixArr = [];
$prefixStr = '';
foreach($newCon as $no => $val) {
    $val = rtrim($val);
    if (!preg_match('/^ /', $val)) {
        $cc= cc($val);
        $prefixArr = [];
        print_r($prefixArr);
        $sortCon[] = $val;
        continue;
    }
    $beforeLineBlanks = blanks($newCon[$no - 1]);
    $currentLineBlanks = blanks($val);
    if ($beforeLineBlanks < $currentLineBlanks) {
        $cc= cc($newCon[$no - 1]);
        $prefixArr[] = $cc;
        print_r($prefixArr);
        $prefixStr = join($separator, $prefixArr);
        $newVal = $prefixStr . $separator . cc($val);
        $sortCon[] = $newVal;
        continue;
    } else if ($beforeLineBlanks == $currentLineBlanks) {
        print_r($prefixArr);
        $prefixStr = join($separator, $prefixArr);
        $newVal = $prefixStr . $separator . cc($val);
        $sortCon[] = $newVal;
        continue;
    } else {
        array_pop($prefixArr);
        print_r($prefixArr);
        $prefixStr = join($separator, $prefixArr);
        $newVal = $prefixStr . $separator . cc($val);
        $sortCon[] = $newVal;
        continue;
    }
}

print_r($sortCon);

// 4
sort($sortCon, SORT_NATURAL);

print_r($sortCon);

// 5
foreach($sortCon as & $val) {
    $no = gtn($val);
    $val = $con[$no];
}

print_r($sortCon);

// 6
$outputFile = 'sort.output';
$con = join('', $sortCon);
file_put_contents($outputFile, $con);

echo $con;


// 干净内容  clean content
function cc($txt) {
    return trim($txt);
}

// 去除结尾 get tail number
function gtn($txt) {
    $tmp = explode('//', $txt);
    return array_pop($tmp);
}

// 上一行开头空格数
function blanks($txt) {
    preg_match('/^ +/', $txt, $match);
    return strlen($match[0]);
}
