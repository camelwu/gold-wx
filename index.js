const Koa = require('koa')
const views = require('koa-views')
const static = require('koa-static')
const Router = require('koa-router')
const path = require('path')
const fs = require('fs')
const app = new Koa()
const co = require('co')
const convert = require('koa-convert')
const json = require('koa-json')
const onerror = require('koa-onerror')
const bodyparser = require('koa-bodyparser')()
const logger = require('koa-logger')

// middlewares
app.use(convert(bodyparser));
app.use(convert(json()));
app.use(convert(logger()));
app.use(static(
  path.join(__dirname, '/static')//fe
))
// logger
app.use(async (ctx, next) => {
  const start = new Date();
  await next();
  const ms = new Date() - start;
  console.log(`${ctx.method} ${ctx.url} - ${ms}ms`);
})
app.use(views(__dirname + '/views', {
  //extension: 'ejs'
  map: {
    html: 'ejs'
  }
}))
let list = JSON.parse(fs.readFileSync(__dirname + '/mock/db.json'))

// 路由x
let router = new Router()
router.get('/', async (ctx, next) => {
  let title = '旅游_旅游网_旅游线路_自助游_自驾游_公司旅游_金桥旅游网',
  enname = '',project = {},
  scenic = [],cjfree = [],news = [],banner = [],cont = list.cont
  await ctx.render('index', {
    title,enname,project,cjfree,scenic,news,banner,cont
  })

}).get('/transactionCodelist', (ctx) => {
  ctx.type = 'json'
  ctx.body = list.news
}).get('/transactionRecord', (ctx) => {
  ctx.type = 'json'
  ctx.body = list.comments
}).get('/404', (ctx) => {
  ctx.body = '404 page!'
}).get('/500', (ctx) => {
  ctx.body = '500 page!'
})
// 加载路由中间件
app
  .use(router.routes())
  .use(router.allowedMethods())
app.on('error', function (err, ctx) {
  console.log(err)
  //log.error('server error', err, ctx);
})
app.listen(8080, () => {
  console.log('[demo] route-use-middleware is starting at port 8080')
})