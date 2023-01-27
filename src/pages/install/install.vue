<template>
<div id="app">
    <el-card class="box-card">
        <el-form label-width="200px">
            <el-form-item label="数据库信息">
                <el-input v-model="form.host" placeholder="请输入数据库地址" style="width: 80%"></el-input>
                <el-input v-model="form.port" placeholder="请输入数据库端口" style="width: 80%"></el-input>
                <el-input v-model="form.name" placeholder="请输入数据库名字" style="width: 80%"></el-input>
                <el-input v-model="form.user" placeholder="请输入数据库用户" style="width: 80%"></el-input>
                <el-input v-model="form.password" placeholder="请输入数据库密码" style="width: 80%"></el-input>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="onSubmit" :loading="isLoading">提交</el-button>
            </el-form-item>
        </el-form>
    </el-card>                               
</div>
</template>

<script>
import axios from 'axios'
export default {
    name: 'install',
    data(){
        return {
            form:{
                host:'127.0.0.1',
                port:'3306',
                name:'',
                user:'',
                password:''
            },
            isLoading:false
        }
    },
    methods:{
        onSubmit(){
            // 是否为空
            if(this.form.host.trim() === "" || this.form.port.trim() === "" || this.form.name.trim() === "" || this.form.user.trim() === "" || this.form.password.trim() === ""){
                this.$message.error("信息不能为空")
                return
            }
            this.isLoading = true
            var params = new URLSearchParams()
            params.append('host', this.form.host)
            params.append('port', this.form.port)
            params.append('name', this.form.name)
            params.append('user', this.form.user)
            params.append('password', this.form.password)
            axios.post('./modules/mySQL/install.php', params).then(
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
    }
}
</script>

<style>

</style>