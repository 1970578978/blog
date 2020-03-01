Vue.component('header-app', {
    data: function () {
        return {
            info:{title:"乡村野娃", sentence:"读书、思考、践行"},
            drawer: false,
            direction: 'ltr',
        }
      },
    template: `
    <div>
    <el-row :gutter="0" class="header">
            <el-col :xs="{span:24,offset:0}" :sm="{span:16,offset:4}" :md="{span:12,offset:6}">
                <div class="herader-title" >
                    <span v-text="info.title"></span>
                    <span class="herader-second" v-text="info.sentence"></span>
                </div>
            </el-col>
        </el-row>

        <el-row :gutter="0" class="hidden-xs-only menus">
            <el-col :xs="{span:24,offset:0}" :sm="{span:16,offset:4}" :md="{span:12,offset:6}">
                <div class="menu" v-cloak>
                    <el-button type="primary"><i class="el-icon-house"></i>首页</el-button>
                    <el-button><i class="el-icon-menu"></i>分类</el-button>
                    <el-button><i class="el-icon-s-management"></i>日志</el-button>
                </div>
            </el-col>
        </el-row>

        <el-row :gutter="0" class="hidden-sm-and-up menus" v-cloak>
            <div class="menu">
                <el-button @click="drawer = true" icon="el-icon-menu" plain>菜单</el-button>
            </div>

            <el-drawer title="菜单" :visible.sync="drawer" :direction="direction" :with-header="false" size="100%">
                <div class="menu menu-heard">
                    菜单
                </div>
                <el-divider></el-divider>
                <div class="menu">
                    <el-button type="primary"><i class="el-icon-house"></i>首页</el-button>
                </div>
                <div class="menu">
                    <el-button><i class="el-icon-menu"></i>分类</el-button>
                </div>
                <div class="menu">
                    <el-button><i class="el-icon-s-management"></i>日志</el-button>
                </div>
                <div class="menu">
                    <el-button @click="drawer = false" type="danger" icon="el-icon-close">关闭</el-button>
                </div>
            </el-drawer>
        </el-row>
        </div>
    `
  });

var head = new Vue({
    el:"#header",
});

axios.get('/js/config/header.json')
    .then(function (response) {
        var data = response.data;
        var title = document.title
        document.title = title + data.title;
        head.$children[0].info = data;
    })
    .catch(function (error) {
});