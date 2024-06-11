<?php
include 'common.php';
include 'header.php';
include 'menu.php';
$stat = Typecho_Widget::widget('Widget_Stat');
$user = Typecho_Widget::widget('Widget_User');
if (!$user->pass('administrator')) {
    die('未登录用户!');
}
?>

<head>
    <style type="text/css">
        .description {
            margin: .5em 0 1em;
            color: #999;
            font-size: .92857em;
        }
        .main-content {
            padding: 20px;
        }
        .check-button {
            margin-bottom: 20px;
        }
        .invalid-links, .valid-links {
            margin-top: 20px;
        }
        .invalid-links {
            float: left;
            width: 45%;
            background: #ffe6e6;
            padding: 10px;
            border-radius: 5px;
        }
        .valid-links {
            float: right;
            width: 45%;
            background: #e6ffe6;
            padding: 10px;
            border-radius: 5px;
        }
        .link-item {
            margin-bottom: 10px;
        }
        .link-item a {
            text-decoration: none;
        }
        .link-item button {
            margin-left: 10px;
        }
        .loading {
            text-align: center;
            font-size: 1.2em;
            color: #555;
        }
    </style>
    <script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.6.0/jquery.js"></script>
    <script type="text/javascript">
        var urls_arr = [];
        var errurls_arr = [];
        var validurls_arr = [];
        var currentIndex = 0;

        function check() {
            $('.loading').show();
            $.ajax({
                type: "GET",
                url: "/action/Linkdetection_action?action=links",
                success: function(data) {
                    urls_arr = JSON.parse(data);
                    checkNext();
                },
                error: function() {
                    alert('无法获取友链数据，请稍后再试。');
                    $('.loading').hide();
                }
            });
        }

        function checkNext() {
            if (currentIndex < urls_arr.length) {
                var value = urls_arr[currentIndex];
                $('#checking-status').text('正在检查: ' + value.name + ' (' + value.url + ')');
                $.ajax({
                    type: "POST",
                    url: "/action/Linkdetection_action?action=check",
                    data: { url: value.url },
                    success: function(res) {
                        if (res !== '200') {
                            errurls_arr.push({ ...value, status: res });
                        } else {
                            validurls_arr.push(value);
                        }
                        currentIndex++;
                        updateDisplay();
                        checkNext();
                    },
                    error: function() {
                        errurls_arr.push({ ...value, status: '检查失败' });
                        currentIndex++;
                        updateDisplay();
                        checkNext();
                    }
                });
            } else {
                $('#checking-status').text('检查完成');
                $('.loading').hide();
                $.ajax({
                    type: "POST",
                    url: "/action/Linkdetection_action?action=post",
                    dataType: "json",
                    data: { data: JSON.stringify(errurls_arr) },
                });
            }
        }

        function updateDisplay() {
            var invalidLinksHtml = '';
            errurls_arr.forEach(function(link) {
                invalidLinksHtml += '<div class="link-item"><a href="' + link.url + '" target="_blank">' + link.name + '</a> 【' + link.status + '】 <button onclick="markInvalid(' + link.lid + ')">设为无效</button></div>';
            });
            $('.invalid-links').html(invalidLinksHtml);

            var validLinksHtml = '';
            validurls_arr.forEach(function(link) {
                validLinksHtml += '<div class="link-item"><a href="' + link.url + '" target="_blank">' + link.name + '</a></div>';
            });
            $('.valid-links').html(validLinksHtml);
        }

        function markInvalid(lid) {
            $.ajax({
                type: "POST",
                url: "/action/Linkdetection_action?action=del",
                data: { data: JSON.stringify([lid]) },
                success: function() {
                    errurls_arr = errurls_arr.filter(link => link.lid !== lid);
                    updateDisplay();
                }
            });
        }
    </script>
</head>
<div class="main">
    <div class="body container">
        <div class="typecho-page-title">
            <h2>异常友链检查</h2>
        </div>
        <div class="main-content">
            <button onclick="check()" class="btn primary check-button">立即检查</button>
            <p class="description">如果友链较多，检查速度可能较慢</p>
            <p id="checking-status"></p>
            <div class="loading" style="display:none;">正在加载，请稍候...</div>
            <div style="display: flow-root;">
            <div class="invalid-links"></div>
            <div class="valid-links"></div>
            </div>
        </div>
    </div>
</div>
<?php
include 'copyright.php';
include 'common-js.php';
include 'footer.php';
?>
