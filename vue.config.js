const { defineConfig } = require('@vue/cli-service')
module.exports = defineConfig({
  transpileDependencies: true,
  lintOnSave: false,
  pages: {
    // 先配置主页
    index: {
        entry: './src/main.js',
        template: './public/index.html',
        title: 'IP探针'
    },
    probe_query: {
        entry: './src/pages/probe_query/probe_query.js',
        template: './public/probe_query.html',
        title: '探针查询'
    },
    install: {
        entry: './src/pages/install/install.js',
        template: './public/install/install.html',
        title: '安装页面'
    }
}
})
