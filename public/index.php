<?php

require __DIR__ . '/../slsstart.php';
$tag = $_GET['tag'] ?? '';
// 生成菜单
$menus = [];
$tagV = [];
$iframes = [];
foreach (SLS_PERM_CONFIGS_GROUPS as $group) {
    $menus[] = '<div class="group"><span>' . $group . '</span>';
    foreach (SLS_PERM_CONFIGS as $k => $v) {
        if ($v['group'] !== $group) {
            continue;
        }
        $tag === $k && $tagV = $v;
        $menus[] = strtr(
            '<a class="{class}" id="btn_{tag}" href="?tag={tag}" onclick="openFrmUrl(\'{tag}\');return false;">{name}</a>',
            [
                '{class}' => $tag === $k ? 'active' : '',
                '{tag}' => $k,
                '{name}' => $v['name'],
            ]
        );
        $iframes[] = strtr(
            '<iframe class="frame hide" id="frm_{tag}" data-url="{url}" src="" frameborder="0"></iframe>',
            [
                '{tag}' => $k,
                '{url}' => "sls_redirect.php?tag=$k"
            ]
        );
    }
    $menus[] = '</div>';
}
$menusHtml = implode(PHP_EOL, $menus);
$iframesHtml = implode(PHP_EOL, $iframes);
$title = 'SLS Dashboard' . (empty($tag) ? '' : (' - ' . $tagV['name']));
$iframeHref = empty($tag) ? '' : "sls_redirect.php?tag=$tag";
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php
        echo $title; ?></title>
    <meta charset="UTF-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge,chrome=1">
    <meta name="viewport"
          content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
    <link rel="shortcut icon" href="./favicon.ico" type="image/x-icon"/>
    <link rel="stylesheet" href="./static/style.css?v=20220520"/>
</head>
<body>
<div class="nav-wrap">
    <div class="top-nav" id="topnav">
        <div><p class="logo">SLS Dashboard</p></div>
        <?php
        echo $menusHtml; ?>
        <div class="right">
            <a class="logout" href="./logout.php">Logout</a>
        </div>
    </div>
</div>
<div style="height:95%">
    <?php
    echo $iframesHtml; ?>
</div>
<div class="loading" id="loading" style="display:none;">
    <div class="shape shape-4">
        <div class="shape-4-top"></div>
        <div class="shape-4-bottom"></div>
        <div class="shape-4-eye"></div>
    </div>
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    <div class="shape shape-3"></div>
</div>
<script>
    var _tag = "<?php echo $tag; ?>";

    function openFrmUrl(tag) {
        var frames = document.getElementsByClassName('frame'),
            links = document.getElementById('topnav').getElementsByTagName('a'),
            frm = document.getElementById('frm_' + tag),
            btn = document.getElementById('btn_' + tag);
        console.log(tag, frm.getAttribute('data-loaded'));
        // 维护按钮
        for (var j = 0; j < links.length; j++) {
            links[j].classList.remove('active');
        }
        btn.classList.add('active');
        // 隐藏所有frame
        for (var i = 0; i < frames.length; i++) {
            frames[i].style.display = 'none';
        }
        // 避免重复加载
        if (!frm.getAttribute('data-loaded')) {
            frm.setAttribute('data-loaded', 1);
            frm.src = frm.getAttribute('data-url');
            loading(true);
        }
        frm.style.display = 'block';
        frm.onload = function () {
            loading(false);
        }
    }

    function loading(flag) {
        var load = document.getElementById('loading');
        if (flag) {
            load.style.display = '';
        } else {
            load.style.display = 'none';
        }
    }

    // 打开默认页
    if (_tag !== '') openFrmUrl(_tag);
</script>
</body>
</html>
