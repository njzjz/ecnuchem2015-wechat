function fetch(time){
	var query = new AV.Query('TreeObject');
	query.descending('createdAt')
	query.limit(15)
	query.lessThan('createdAt', time)
	query.find().then(function (comments) {
		var html=''
		for (var i = 0; i < comments.length; i++) {
			var comment = comments[i];
			var content = comment.get('content');
			var createdAt = comment.createdAt
			html+="<section class='treetable tree'>"+content+"<br/>--"+createdAt+"</section>"
		}
		$("#loading").hide()
		$("#main").append(html)
		$("#more").show()
		$("#more").unbind('click').click(function(event) {
			fetch(createdAt)
		});
	}, function (error) {
		$("#loading").html("加载错误！")
	});
}
function write(){
	var TodoFolder = AV.Object.extend('TreeObject')
	var todoFolder = new TodoFolder()
	var content=$("#input").val()
	todoFolder.set('content',content)
	todoFolder.save().then(function (todo) {
		$("#dialog2").show()
		var createdAt = todo.createdAt
		var html="<section class='treetable tree'>"+content+"<br/>--"+createdAt+"</section>"+$("#main").html()
		$("#main").html(html)
	}, function (error) {
		$("#dialog3").show()
	})
}

$(function(){
	var time=new Date()
	fetch(time)
})