<template>
<div id="app">
<el-menu default-active="2" class="el-menu-demo" mode="horizontal" @select="selectHandle">
    <el-menu-item index="1">IP探针</el-menu-item>
    <el-menu-item index="2">探针查询</el-menu-item>
</el-menu>
<el-form label-width="80px">
    <el-form-item label="查询密钥">
        <el-input v-model="form.key" placeholder="请输入查询密钥" style="width: 80%"></el-input>
        <el-button type="primary" @click="onSubmit" :loading="isLoading">提交</el-button>
    </el-form-item>
</el-form>

<el-table
    :data="tableData"
    border
    style="width: 100%">
    <el-table-column
        fixed
        prop="id"
        label="id"
        width="50">
    </el-table-column>
    <el-table-column
        prop="keyvalue"
        label="key"
        width="50">
    </el-table-column>
    <el-table-column
        prop="timestamp"
        label="日期"
        width="100">
    </el-table-column>
    <el-table-column
        prop="ip"
        label="ip"
        width="100">
    </el-table-column>
    <el-table-column
        label="ip经度"
        prop="ipjing"
        width="180">
    </el-table-column>
    <el-table-column
    label="ip维度"
    prop="ipwei"
        width="180">
    </el-table-column>
    <el-table-column
    label="ip位置"
    prop="ipaddress"
        width="180">
    </el-table-column>
    <el-table-column
    label="gps经度"
    prop="gpsjing"
        width="180">
    </el-table-column>
    <el-table-column
    label="gps维度"
    prop="gpswei"
        width="180">
    </el-table-column>
    <el-table-column
    label="gps位置"
    prop="gpsaddress"
        width="180">
    </el-table-column>
    <el-table-column
    label="图片位置"
    prop="cameraphoto"
        width="180">
    </el-table-column>
    <el-table-column
    label="浏览器语言"
    prop="language"
        width="100">
    </el-table-column>
    <el-table-column
    label="浏览器类型"
    prop="type"
        width="100">
    </el-table-column>
    <el-table-column
    label="浏览器UA"
    prop="UA"
        width="90">
    </el-table-column>
    <el-table-column
    label="手机系统"
    prop="system"
        width="80">
    </el-table-column>
    <el-table-column
        label="操作"
        width="100">
        <template slot-scope="scope">
        <el-button type="text" size="small"><a :href="scope.row.cameraphoto" target="_blank" >下载相机图片</a></el-button>
        </template>
</el-table-column>
    </el-table>
    <el-divider></el-divider>
        <span>Hurry 2022-2023</span>
</div>
</template>

<script>
import axios from 'axios'
export default {
    name: 'probe_query',
    methods:{
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
        },
        onSubmit(){
            if(this.form.key.trim() == ""){
                this.$message.error("Key不能为空")
                return
            }
            this.isLoading = true
            var params = new URLSearchParams()
            params.append('key', this.form.key)
            axios.post('./modules/webModules/probe_query.php', params).then(
                response => {
                    if(response.data.code === 200){
                    this.isLoading=false
                    this.$message({
                        message: response.data.msg,
                        type: 'success'
                    })
                    this.tableData = response.data.data
                }else{
                this.isLoading=false
                this.$message.error(response.data.msg)
                }},
                error => {
                    this.isLoading=false
                    this.$message.error(error.message)
                }
            )
        }
    },
    data(){
        return {
            form:{
                key:''
            },
            tableData:[],
            isLoading:false
        }
    }
}
</script>

<style>
</style>
