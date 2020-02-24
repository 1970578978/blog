Vue.component('footer-rwb', {
    data: function () {
        return {
          footer:{copyright:"", record:"", link:""}
        }
      },
    template: `
    
        <el-row :gutter="0">
            <el-col :xs="{span:22,offset:1}" :sm="{span:16,offset:4}" :md="{span:12,offset:6}">
                <div class="footer">
                    <span v-text="footer.copyright"></span>
                    <a v-bind:href="footer.link" target="_blank">/<span v-text="footer.record"></span></a>
                </div>
            </el-col>
        </el-row>
    `
  });

var foot = new Vue({
    el:"#footer",
    data:{
        footer:{copyright:"aaaaa", record:"bbbbb", link:""}
    }
});

axios.get('/js/config/footer.json')
    .then(function (response) {
        var con = response.data;
        foot.$children[0].footer = con;
    })
    .catch(function (error) {
});