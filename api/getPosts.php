<?php
    include_once "../sqlHelper.php";
    // print_r($_GET);
    //1、获取当前页数
    //因为刚加载文章数据的时候没有带page参数，给设置一个默认为1
    $page = isset($_GET["page"])?$_GET["page"]:1;

    //1.1、自定义每页显示的条数
    $pagesize = 10;
    //1.2、定义limit查询的起始位置
    $offset = ($page-1)*$pagesize;
     //2、编写sql语句查询所需数据
    $sql = "SELECT
     t1.*,t2.nickname,t3.cat_name
    FROM
     posts t1
    LEFT JOIN users t2
    on
     t1.user_id = t2.user_id
    LEFT JOIN category t3
    ON
     t1.cat_id = t3.cat_id
    ORDER BY t1.post_id DESC
    LIMIT $offset,$pagesize";

     //3、执行sql语句
     $data = read($sql,"bx");

     //4、定义sql语句获取文章总数,算出分页码数
     $sql2 = "select count(*) postsCount from posts";
     //执行sql语句
     $res2 = read($sql2,"bx");
     $postsCount = $res2[0]["postsCount"];
     $pageCount = ceil($postsCount/$pagesize);
    //  var_dump($postCount);
    //  array(1) {
    //     [0]=>
    //     array(1) {
    //       ["postsCount"]=>
    //       string(4) "1004"
    //     }
    //   }
    //  echo json_encode($data);

     //5、响应json数据给前端页面进行渲染
     if ($data) {
        $response = ['code'=>200,'message'=>'获取数据成功','data'=>$data,'pageCount'=>$pageCount];
     } else {
         $response = ['code'=>-1,'message'=>'获取数据失败'];
     }

     echo json_encode($response);
?>