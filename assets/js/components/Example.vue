<template>
   <div>
    
    <p>This is an example of a new components in {{name}}</p>

    <el-row>
    <el-button @click="visible = true">Open dialog</el-button>
    <el-button type="primary">Primary</el-button>
    <el-button type="success">Success</el-button>
    <el-button type="info">Info</el-button>
    <el-button type="warning">Warning</el-button>
    <el-button type="danger">Danger</el-button>
    </el-row>
    <el-row>
    <el-button icon="el-icon-search" circle></el-button>
    <el-button type="primary" icon="el-icon-edit" circle></el-button>
    <el-button type="success" icon="el-icon-check" circle></el-button>
    <el-button type="info" icon="el-icon-message" circle></el-button>
    <el-button type="warning" icon="el-icon-star-off" circle></el-button>
    <el-button type="danger" icon="el-icon-delete" circle></el-button>
    </el-row>    

    <el-dialog :visible.sync="visible" title="Hello world">
      <p>Try Element</p>
    </el-dialog>

   </div>
</template>

<script>
   export default {
       name: "example",
        data: function() {
            return {
                name : '--VueJs--', 
                visible: false 
            }
        }       
   }
</script>

<style scoped>

</style>