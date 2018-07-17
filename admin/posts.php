<?php 
  //判断服务器的session中是否存了相关数据
  session_start();//一定要先开启
  // var_dump($_SESSION['user']);
  if (!isset($_SESSION["user"])) {
    header("location:./login.php");
    // print_r("请先登录");
    exit;
  }
  
  //给当前页设置一个标志
  $visitor = "posts";
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Posts &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
</head>
<body>
  <div class="main">
    <nav class="navbar">
      <button class="btn btn-default navbar-btn fa fa-bars"></button>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="profile.php"><i class="fa fa-user"></i>个人中心</a></li>
        <li><a href="../api/logout.php" onclick="return confirm('确认退出吗？')"><i class="fa fa-sign-out"></i>退出</a></li>
      </ul>
    </nav>
    <div class="container-fluid">
    <div class="page-title">
        <h1>所有文章</h1>
        <a href="post-add.html" class="btn btn-primary btn-xs">写文章</a>
      </div>
    <ul id="pagination-demo" class="pagination-sm pull-right pagination"></ul>
    <div class="page-action">
    <a class="btn btn-danger btn-sm" href="javascript:;" style="visibility: hidden;">批量删除</a>
    <form class="form-inline">
          <select name="" class="form-control input-sm">
            <option value="">所有分类</option>
            <option value="">未分类</option>
          </select>
          <select name="" class="form-control input-sm">
            <option value="">所有状态</option>
            <option value="">草稿</option>
            <option value="">已发布</option>
            <option value="">已删除</option>
          </select>
          <span class="btn btn-default btn-sm">筛选</span>
        </form>
        </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->

        <!-- show when multiple checked -->
        
      
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>标题</th>
            <th>作者</th>
            <th>分类</th>
            <th class="text-center">发表时间</th>
            <th class="text-center">状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>

  <?php 
    include_once "./commonAside.php";
  ?>
  
  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/plugins/jquery.twbsPagination.min.js"></script>
  <script src="/static/assets/vendors/art-template/template-web.js"></script>
  <!-- 准备一个模板引擎 -->
  <script type="text/x-art-template" id="tmpl">
    {{each comments}}
    <tr>
      <td class="text-center"><input type="checkbox" value="{{$value["post_id"]}}"></td>
      <td>{{$value["title"]}}</td>
      <td>{{$value["nickname"]}}</td>
      <td>{{$value["cat_name"]}}</td>
      <td class="text-center">{{$value["created"]}}</td>
      <td class="text-center">{{$value["status"]}}</td>
      <td class="text-center">
        <a href="javascript:;" class="btn btn-default btn-xs">编辑</a>
        <a href="javascript:;" class="btn btn-danger btn-xs">删除</a>
      </td>
    </tr>
    {{/each}}
  </script>
</body>
<script>
  //在posts.php页面的下面发送ajax请求，获取文章的数据,使用模板引擎进行数据的渲染
  $.ajax({
    dataType:"json",
    type:"get",
    url:"../api/getPosts.php",
    success:function(res){
      if (res.code == 200) {
        var data = res.data;
        var pageCount = res.pageCount;
        var context = {comments:data}
        //借助与模板引擎的api
        var html = template('tmpl',context);
        $("tbody").html(html);

        //1、初始化成功之后，为分页导航初始化并注册点击事件
        //1.1、绘制分页导航
        pageList(pageCount);
      }
    },
    error:function(){
      console.log("失败");
    }
  });

  function pageList(pageCount) {
    //模板需要的数据
  $('#pagination-demo').twbsPagination({
    totalPages: pageCount,
    visiblePages: 7,
    initiateStartPageClick:false, // 取消默认初始点击
    onPageClick: function (event, page) {
        // $('#page-content').text('Page ' + page);
        $.ajax({
          dataType:"json",
          type:"get",
          url:"../api/getPosts.php",
          data:{
            'page':page
          },
          success:function (res){
            if (res.code == 200) {
              console.log("成功");
              var data = res.data;
              var context = {comments:data}
              //借助与模板引擎的api
              var html = template('tmpl',context);
              $("tbody").html(html);
            }
          },
          error:function () {
            console.log("最终失败");
          }
        });
    }
}); 
}
</script>
</html>