<?php

// 失败部分重新处理
// buchong('23593,23594,23595,23596,23597,23598,23599,23600,23601,23602,23603,23604,23605,23606,23607,23608,23609,56730,56731,56732,1387,1396,1405,1413,1421,1432,1441,2980,2983,1458,1466,1475');

// 开始
main();

function main()
{
    $dbh = new PDO('mysql:host=127.0.0.1;port=3306;dbname=kingfisher;charset=utf8', 'root', '123456');
    $stat1 = $dbh->prepare('insert into china_cities (oid,name,pid,layer,sort,status) values(?,?,?,?,1,1)');

    $rows = [
        ['id' => 1, 'name' => 北京], ['id' => 2, 'name' => 上海], ['id' => 3, 'name' => 天津], ['id' => 4, 'name' => 重庆], ['id' => 5, 'name' => 河北], ['id' => 6, 'name' => 山西], ['id' => 7, 'name' => 河南], ['id' => 8, 'name' => 辽宁], ['id' => 9, 'name' => 吉林], ['id' => 10, 'name' => 黑龙江], ['id' => 11, 'name' => 内蒙古], ['id' => 12, 'name' => 江苏], ['id' => 13, 'name' => 山东], ['id' => 14, 'name' => 安徽], ['id' => 15, 'name' => 浙江], ['id' => 16, 'name' => 福建], ['id' => 17, 'name' => 湖北], ['id' => 18, 'name' => 湖南], ['id' => 19, 'name' => 广东], ['id' => 20, 'name' => 广西], ['id' => 21, 'name' => 江西], ['id' => 22, 'name' => 四川], ['id' => 23, 'name' => 海南], ['id' => 24, 'name' => 贵州], ['id' => 25, 'name' => 云南], ['id' => 26, 'name' => 西藏], ['id' => 27, 'name' => 陕西], ['id' => 28, 'name' => 甘肃], ['id' => 29, 'name' => 青海], ['id' => 30, 'name' => 宁夏], ['id' => 31, 'name' => 新疆], ['id' => 32, 'name' => 台湾], ['id' => 42, 'name' => 香港], ['id' => 43, 'name' => 澳门], ['id' => 84, 'name' => 钓鱼岛]
    ];
    foreach ($rows as $row) {
        $le = 1;
        $pid = 0;
        $stat1->bindParam(1, $row['id']);
        $stat1->bindParam(2, $row['name']);
        $stat1->bindParam(3, $pid);
        $stat1->bindParam(4, $le);
        $stat1->execute();

        $arr = getCity($row['id']);
        if (!empty($arr)) {
            insert($arr, $row['id'], $le + 1, $stat1);
        }
    }
}

function buchong($str)
{
    $dbh = new PDO('mysql:host=127.0.0.1;port=3306;dbname=kingfisher;charset=utf8', 'root', '123456');
    $stat1 = $dbh->prepare('insert into china_cities (oid,name,pid,layer,sort,status) values(?,?,?,?,1,1)');

    $arr_pid = explode(',', $str);
    foreach ($arr_pid as $pid) {
        $arr = getCity($pid);
        if (!empty($arr)) {
            foreach ($dbh->query("select * from china_cities where oid = " . $pid) as $row) {
                insert($arr, $pid, $row['layer'] + 1, $stat1);
            }
        }
    }
}


function getCity($pid)
{
    $url = 'https://d.jd.com/area/get?fid=' . $pid;
    $opts = array(
        'http' => array(
            'method' => "GET",
            'timeout' => 5,
        )
    );
    $context = stream_context_create($opts);
    for ($i = 1; $i < 5; $i++) {
        $str = file_get_contents($url, false, $context);
        if ($str !== false) {
            return json_decode($str, true);
        }
    }

    file_put_contents('city.log', $pid . ',', FILE_APPEND);
    echo $pid . ',';
}

function insert($rows, $pid, $le, $stat1)
{
    foreach ($rows as $row) {
        $stat1->bindParam(1, $row['id']);
        $stat1->bindParam(2, $row['name']);
        $stat1->bindParam(3, $pid);
        $stat1->bindParam(4, $le);
        $stat1->execute();

        if ($le + 1 >= 5) {
            continue;
        }
        $arr = getCity($row['id']);
        if (!empty($arr)) {
            insert($arr, $row['id'], $le + 1, $stat1);
        }
    }
}
