//npm install cnpm -g --registry=https://registry.npm.taobao.org
//cnpm init
//cnpm install gulp --save-dev
//cnpm install gulp-minify-css gulp-concat gulp-rename gulp-notify gulp-livereload --save-dev
//cnpm install jshint@2.x gulp-jshint gulp-uglify --save-dev
// 引入 gulp
var gulp = require('gulp');
 
// 引入组件
var 
    minifycss = require('gulp-minify-css'),//css压缩
    jshint = require('gulp-jshint'),//js检测
    uglify = require('gulp-uglify'),//js压缩
    concat = require('gulp-concat'),//文件合并
    rename = require('gulp-rename'),//文件更名
    notify = require('gulp-notify');//提示信息
    livereload = require('gulp-livereload');//网页自动刷新
// 合并、压缩、重命名css
gulp.task('css', function() {
  return gulp.src(['css/module/*.css','css/unit/*.css','css/page/*.css'])
        .pipe(concat('fanwe.css'))
        .pipe(gulp.dest('css/dist'))
        /*.pipe(rename({ suffix: '.min' }))
        .pipe(minifycss())
        .pipe(gulp.dest('css/dist'))*/
        .pipe(notify({ message: 'css task ok' }));
});
 
// 检查js
gulp.task('lint', function() {
  return gulp.src('js/*.js')
    .pipe(jshint())
    .pipe(jshint.reporter('default'))
    .pipe(notify({ message: 'lint task ok' }));
});
// 合并、压缩js文件
gulp.task('js', function() {
  return gulp.src(['js/module/*.js','js/unti/*.js','js/page/*.js'])
    .pipe(concat('fanwe.js'))
    .pipe(gulp.dest('js/dist'))
    .pipe(rename({ suffix: '.min' }))
    .pipe(uglify())
    .pipe(gulp.dest('js/dist'))
    .pipe(notify({ message: 'js task ok' }));
});   

// 默认任务
gulp.task('default', function(){
 	gulp.run(['css','js']);
	livereload.listen();
	gulp.watch(['css/module/*.css','css/unit/*.css','css/page/*.css'], ['css']);
    gulp.watch(['js/module/*.js','js/unit/*.js','js/page/*.js'], ['js']);

});