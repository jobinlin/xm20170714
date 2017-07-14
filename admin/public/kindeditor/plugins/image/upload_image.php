<?php
define("FILE_PATH","/admin/public/kindeditor/plugins/image"); //文件目录，空为根目录
require_once '../../../../../system/system_init.php';
require_once(APP_ROOT_PATH.'admin/public/kindeditor/check.php');
?>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<title>Image</title>
		<style type="text/css" rel="stylesheet">
			body {
				font-size:12px;
				font-family: "sans serif",tahoma,verdana,helvetica;
				margin:0;
				background-color:#F0F0EE;
				overflow:hidden;
			}
			form {
				margin:0;
			}
			label {
				cursor:pointer;
			}
			#resetBtn {
				margin-left:10px;
				cursor:pointer;
			}
			.main {
				margin: 10px;
			}
			.tab-navi {
				width:100%;
				overflow:hidden;
				margin-bottom:10px;
			}
			.tab-navi ul  {
				list-style-image:none;
				list-style-position:outside;
				list-style-type:none;
				margin:0;
				padding:0;
				display:block;
				float:left;
				width:100%;
				border-bottom:1px solid #888888;
			}
			.tab-navi li {
				border: 1px solid #888888;
				margin:0 -1px -1px 0;
				float: left;
				padding: 5px;
				background-color: #F0F0EE;
				text-align: center;
				width: 120px;
				font-weight: normal;
				cursor: pointer;
			}
			.tab-navi li.selected {
				background-color: #E0E0E0;
				font-weight: bold;
				cursor: default;
			}
			.table  {
				list-style-image:none;
				list-style-position:outside;
				list-style-type:none;
				margin:0;
				padding:0;
				display:block;
			}
			.table li {
				padding:0;
				margin-bottom:10px;
				display:list-item;
			}
			.table li label {
				font-weight:bold;
			}
			.table li input {
				vertical-align:middle;
			}
			.table li img {
				vertical-align:middle;
			}
		</style>		
		<script type="text/javascript">
			var KE = parent.KE;
			location.href.match(/\?id=([\w-]+)/i);
			var id = RegExp.$1;
			var fileManager = null;
			var allowUpload = (typeof KE.g[id].allowUpload == 'undefined') ? true : KE.g[id].allowUpload;

			<?php
			if($GLOBALS['distribution_cfg']['OSS_TYPE']&&$GLOBALS['distribution_cfg']['OSS_TYPE']!="NONE")
			{
				echo "var allowFileManager = false;";
			}
			else
			{
				echo "var allowFileManager = (typeof KE.g[id].allowFileManager == 'undefined') ? false : KE.g[id].allowFileManager;";
			}
			?>
			
			var referMethod = (typeof KE.g[id].referMethod == 'undefined') ? '' : KE.g[id].referMethod;
			KE.event.ready(function() {
				var typeBox = KE.$('type', document);
				var urlBox = KE.$('url', document);
				var alignElements = document.getElementsByName('align');
				var fileBox = KE.$('imgFile', document);
				var widthBox = KE.$('imgWidth', document);
				var heightBox = KE.$('imgHeight', document);
				var titleBox = KE.$('imgTitle', document);
				var resetBtn = KE.$('resetBtn', document);
				var tabNavi = KE.$('tabNavi', document);
				var viewServer = KE.$('viewServer', document);
				var liList = tabNavi.getElementsByTagName('li');
				var selectTab = function(num) {
					if (num == 1) resetBtn.style.display = 'none';
					else resetBtn.style.display = '';
					widthBox.value = '';
					heightBox.value = '';
					titleBox.value = '';
					alignElements[0].checked = true;
					for (var i = 0, len = liList.length; i < len; i++) {
						var li = liList[i];
						if (i === num) {
							li.className = 'selected';
							li.onclick = null;
						} else {
							if (allowUpload) {
								li.className = '';
								li.onclick = (function (i) {
									return function() {
										if (!fileManager) selectTab(i);
									};
								})(i);
							} else {
								li.parentNode.removeChild(li);
								
							}
						}
						KE.$('tab' + (i + 1), document).style.display = 'none';
					}
					typeBox.value = num + 1;
					KE.$('tab' + (num + 1), document).style.display = '';
					if (!allowFileManager) {
						var netImg = KE.$('netImg', document);
						//netImg.parentNode.removeChild(netImg);
						netImg.style.display = 'none';
						typeBox.value=2;
						
						KE.$('tab1', document).style.display = 'none';
						KE.$('tab2', document).style.display = '';
					}
				}
				
				selectTab(0);
				var imgNode = KE.plugin['image'].getSelectedNode(id);
				if (imgNode) {
					var src = KE.format.getUrl(imgNode.src, KE.g[id].urlType);
					urlBox.value = src;
					widthBox.value = imgNode.width;
					heightBox.value = imgNode.height;
					titleBox.value = (typeof imgNode.alt != 'undefined') ? imgNode.alt : imgNode.title;
					for (var i = 0, len = alignElements.length; i < len; i++) {
						if (alignElements[i].value == imgNode.align) {
							alignElements[i].checked = true;
							break;
						}
					}
				}
				KE.event.add(viewServer, 'click', function () {
					
					if (fileManager) return false;
					fileManager = new KE.dialog({
						id : id,
						cmd : 'file_manager',
						file : 'file_manager/file_manager.php?id=' + id +'&root=<?php echo htmlspecialchars(addslashes($_REQUEST['root']));?>',
						width : 500,
						height : 400,
						loadingMode : true,
						title : '浏览服务器',
						noButton : '取消',
						afterHide : function() {
							fileManager = null;
						}
					});
					fileManager.show();
				});
				KE.$('referMethod', document).value = referMethod;
				var alignIds = ['default', 'left', 'right'];
				for (var i = 0, len = alignIds.length; i < len; i++) {
					KE.event.add(KE.$(alignIds[i] + 'Img', document), 'click', (function(i) {
						return function() {
							KE.$(alignIds[i] + 'Chk', document).checked = true;
						};
					})(i));
				}
				KE.event.add(resetBtn, 'click', function() {
					var g = KE.g[id];
					var img = KE.$$('img', g.iframeDoc);
					img.src = urlBox.value;
					img.style.position = 'absolute';
					img.style.visibility = 'hidden';
					img.style.top = '0px';
					img.style.left = '1000px';
					g.iframeDoc.body.appendChild(img);
					widthBox.value = img.width;
					heightBox.value = img.height;
					g.iframeDoc.body.removeChild(img);
				});
				KE.util.hideLoadingPage(id);
			}, window, document);
		</script>
	</head>
	<?php 
	error_reporting(0);
	$file_url = $_REQUEST['file']?htmlspecialchars(addslashes(trim($_REQUEST['file']))):'';
	if($file_url=='')$file_url = 'http://'; 
	?>
	<body>
		<div class="main">
		
			<div id="tabNavi" class="tab-navi">
				<ul>
					<li id="netImg">网络上的图片</li>
					<li id="localImg">电脑里的图片</li>
				</ul>
			</div>
			<iframe name="uploadIframe" id="uploadIframe" style="display:none;"></iframe>
			<input type="hidden" id="type" name="type" value="" />
			<form name="uploadForm" method="post" enctype="multipart/form-data" target="uploadIframe" action="<?php echo htmlspecialchars(addslashes($_REQUEST['root']));?>">
				<input type="hidden" id="editorId" name="id" value="<?php echo htmlspecialchars(addslashes($_REQUEST['id']));?>" />
				<input type="hidden" id="referMethod" name="referMethod" value="" />
				<input type="hidden" name="imgBorder" value="0" />
				<input type="hidden" name="<?php echo htmlspecialchars(addslashes($_REQUEST['var_module']));?>" value="File" />
				<input type="hidden" name="<?php echo htmlspecialchars(addslashes($_REQUEST['var_action']));?>" value="do_upload_img" />
				<ul class="table">
					<li>
						<div id="tab1" style="display:none;">
							<label for="url">图片地址</label>
							<input type="text" id="url" name="url" value="<?php echo $file_url;?>" maxlength="255" style="width:250px;" />
							<input type="button" id="viewServer" name="viewServer" value="浏览..." />
						</div>
						<div id="tab2" style="display:none;">
							<select name="upload_type">
							<option value="0">普通上传</option>
							<option value="1">水印上传</option>							
							</select>
							<input type="file" id="imgFile" name="imgFile" style="width:200px;" />&nbsp;&nbsp;
							
						</div>
					</li>
					<div style="display:none;">
					<li>
						<label for="imgWidth">图片大小</label>
						宽 <input type="text" id="imgWidth" name="imgWidth" value="" maxlength="4" style="width:50px;text-align:right;" />
						高 <input type="text" id="imgHeight" name="imgHeight" value="" maxlength="4" style="width:50px;text-align:right;" />
						<img src="./images/refresh.gif" width="16" height="16" id="resetBtn" alt="重置大小" title="重置大小" />
					</li>
					<li>
						<label>对齐方式</label>
						<input type="radio" id="defaultChk" name="align" value="" checked="checked" /> <img id="defaultImg" src="./images/align_top.gif" width="23" height="25" border="0" alt="默认方式" title="默认方式" />
						<input type="radio" id="leftChk" name="align" value="left" /> <img id="leftImg" src="./images/align_left.gif" width="23" height="25" border="0" alt="左对齐" title="左对齐" />
						<input type="radio" id="rightChk" name="align" value="right" /> <img id="rightImg" src="./images/align_right.gif" width="23" height="25" border="0" alt="右对齐" title="右对齐" />
					</li>
					<li>
						<label for="imgTitle">图片说明</label>
						<input type="text" id="imgTitle" name="imgTitle" value="" maxlength="255" style="width:95%;" />
					</li>
					</div>
				</ul>
			</form>
		</div>
	</body>
</html>
