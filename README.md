# 阿里百秀项目

> where="1=1"--组装where查询条件  

-  where = '1=1' 条件永远为真，当后面没有and拼接条件的时候，sql就这样 ：select*from posts where

-  where后面没有条件就会报错，where后面拼接1=1,就避免这种错，这种语句一般用在搜索中，主要防止查询条件报错

```sql
$where = "1+1";//用于拼接查询条件
    if ( $cat_id != 'all' ) {
        $where .= " and t1.cat_id = $cat_id ";
    }
    if ( $status != 'all' ) {
        $where .= " and t1.status = '$status'";
    }

     //2、编写sql语句查询所需数据
    $where = '1=1';//用于拼接查询条件
    if ( $cat_id != 'all' ) {
        $where .= " and t1.cat_id = $cat_id ";
    }
    if ( $status != 'all' ) {
        $where .= " and t1.status = '$status'";
    }


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
    WHERE $where
    ORDER BY t1.post_id DESC
    LIMIT $offset,$pagesize";

     //3、执行sql语句
     $data = read($sql,"bx");

     //4、定义sql语句获取文章总数,算出分页码数
     $sql2 = "SELECT count(*) postsCount FROM posts t1 WHERE $where";
```

## 模板引擎的使用

> 1、预备知识点：  

### script标签的特点是：
- innerHTML 永远不会显示在界面上
- 如果 type 不等于 text/javascript 的话，内部的内容不会作为 javascript执行

>选择一个模板引擎

[推荐]https://github.com/tj/consolidate.js#supported-template-engines

> 2、下载模板引擎JS文件  


template-web.js

>3、引入到页面

```js
<script src="/static/assets/vendors/art-template/template-web.js"></script>
```

>4、准备一个模板 (联系script标签的特点)

```js
editor.setTheme('dark');
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
  ```

  > 5、准备一个数据

  ```js
   $.ajax({
    dataType:"json",
    type:"get",
    url:"../api/getPosts.php",
    data:{
      cat_id:cat_id,
      status:status
    },
    success:function(res){
      if (res.code == 200) {
        console.log("请学会给自己找麻烦");
        var data = res.data;
        //调用模板引擎渲染数据
        var context = {comments:data}
        //借助模板引擎的api
        var html = template('tmpl',context);
        //将渲染结果的html设置到默认元素的innerHTML中
        $("tbody").html(html);
        // 重新绘制分页导航
        pageList(res.pageCount);
      }
    },
    error:function(){
      console.log("失败");
    },
    complete:function(){}
  });
  });
  ```

  > 6、通过模板引擎的js提供大的一个api将模板整合得到渲染结果HTML

  ```js
  //调用模板引擎渲染数据
  var context = {comments:data}
  //借助模板引擎的api
  var html = template('tmpl',context);
 ```

 >将渲染结果的HTML设置到默认元素的html中

 ```js
 //将渲染结果的html设置到默认元素的innerHTML中
          $("tbody").html(html);
 ```

## Pagination plugin(分页插件介绍)

> 1、推荐插件下载网址                                    

[ajax分页插件的网址](http://esimakin.github.io/twbs-pagination/)

> 2、进行下载下来，引入下面的js文件：jquery.twbsPagination.js

> 3、放入到项目位置中去

> 4、HTML code

```html
<ul id="pagination-demo" class="pagination-sm pull-right pagination"></ul>
```
> 5、JS code

```js
$('#pagination-demo').twbsPagination({
      totalPages: pageCount,//分页页码的总页数
      visiblePages: 7,//展示的页码数
      initiateStartPageClick:false, // 取消默认初始点击
      onPageClick: function (event, page) {
          // $('#page-content').text('Page ' + page);
      }
});
```

> 6、**项目中要注意的的点**

- **重置分页页码，要重新渲染筛选条件后的分页页码,对page进行解绑事件**
- **因为只需要初始化一次**
```js
//重置分页页码，要重新渲染筛选条件后的分页页码,对page进行解绑事件
    $("#pagination-demo").empty();
    //删除此插件自带的一个值
    $("#pagination-demo").removeData('twbs-pagination');
    //解绑page事件
    $("#pagination-demo").unbind('page');
```

