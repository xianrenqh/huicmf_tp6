加载组件

layui.use(['element', 'jquery', 'loading'], function() {
 	var element = layui.element;
	var $ = layui.jquery;
	var loading = layui.loading;

$(".loading-1").click(function() {

					loading.Load(1, "");

					loading.loadRemove(1000);
				})

				$(".loading-2").click(function() {

					loading.Load(2, "");

					loading.loadRemove(1000);
				})

				$(".loading-3").click(function() {

					loading.Load(3, "");

					loading.loadRemove(1000);
				})

				$(".loading-4").click(function() {

					loading.Load(4, "");

					loading.loadRemove(1000);
				})

				$(".loading-5").click(function() {

					loading.Load(5, "");

					loading.loadRemove(1000);
				})

				$(".block-1").click(function() {

					loading.block({
						type: 1,
						elem: '.load-div-1',
						msg: '加载中'
					})
					loading.blockRemove(".load-div-1", 1000);
				})

				$(".block-2").click(function() {
					loading.block({
						type: 2,
						elem: '.load-div-1',
						msg: ''
					})
					loading.blockRemove(".load-div-1", 1000);
				})

				$(".block-3").click(function() {
					loading.block({
						type: 3,
						elem: '.load-div-1',
						msg: ''
					})
					loading.blockRemove(".load-div-1", 1000);
				})

				$(".block-4").click(function() {
					loading.block({
						type: 4,
						elem: '.load-div-1',
						msg: ''
					})
					loading.blockRemove(".load-div-1", 1000);
				})

				$(".block-5").click(function() {
					loading.block({
						type: 5,
						elem: '.load-div-1',
						msg: ''
					})
					loading.blockRemove(".load-div-1", 1000);
				})

				$(".block-6").click(function() {
					loading.block({
						type: 6,
						elem: '.load-div-1',
						msg: ''
					})
					loading.blockRemove(".load-div-1", 1000);
				})

});