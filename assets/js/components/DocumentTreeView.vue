<template>
   <div>
    <!--p>DocumentTreeView {{name}}</p-->

    <!--el-row>
    <el-button icon="el-icon-search" circle @click="visible = true"></el-button>
    <el-button type="primary" icon="el-icon-edit" circle></el-button>
    <el-button type="success" icon="el-icon-check" circle></el-button>
    <el-button type="info" icon="el-icon-message" circle></el-button>
    <el-button type="warning" icon="el-icon-star-off" circle></el-button>
    <el-button type="danger" icon="el-icon-delete" circle></el-button>
    </el-row>    

    <el-dialog :visible.sync="visible" title="Hello world">
      <p>Try Element</p>
    </el-dialog-->

    <div v-loading="loading">
        <el-row :span="24">
            <el-col :span="24" style="height: 8vh; border: 1px solid #eee;">
                <el-radio-group v-for="docfile in docfiles" :key="docfile" v-model="docselect">
                    <el-radio-button type="success" style="margin:3px" :label="docfile">{{ docfile }}</el-radio-button>
                </el-radio-group>
            </el-col>
        </el-row>

        <el-row :span="24">
            <el-col :span="8">
                <el-tree :data="treejson" :props="defaultProps" style="height: 90vh; border: 1px solid #eee; overflow-y: scroll;">
                    <span class="custom-tree-node" slot-scope="{ data }">
                        <span v-on:click="TreeNodeClick(data)"> 
                            <a :href="'#'+data.id" style="text-decoration: none; color: #555">{{ data.label }}</a>
                        </span>
                    </span>
                </el-tree>
            </el-col>
            <el-col :span="16" style="height: 90vh; border: 1px solid #eee; overflow-y: scroll;">
                <!--{{TreeNodeSelectText}}<br/><br/><br/-->
                <span v-html="treehtml"></span>
            </el-col>
        </el-row>
    </div>

    <!--p>{{treejson}}</p-->

   </div>
</template>

<script>
import axios from 'axios';

   export default {
       name: "documenttreeview_ne_vazhno",
        data: function() {
            return {
                name: '--VueJs--', 
                visible: false,
                docfiles: null,
                docselect: null,
                treejson: null,
                //treejson2: null,
                TreeNodeSelectText: "пусто",
                treehtml: "",
                loading: false,
                defaultProps: {
                    id: 'id',
                    children: 'children',
                    label: 'label',
                    text: 'text'
                }
            }
        },
        mounted(){
            this.Query('*');
        },
        watch:{
            docselect: function(val){
                //alert('select '+val1);
                this.Query(val);
            },
        },
        methods: {
            TreeNodeClick: function(node){
                //alert('click '+this.label);
                //console.log(node.data.text);
                this.TreeNodeSelectText=node.text
            },
            /*FileClick: function(file){
                alert('click '+file);
                this.Query(file);
                //console.log(node.data.text);
                //this.TreeNodeSelectText=node.data.text
            },*/
            Query: function(mask){
                this.loading=true;
                axios
                .get('http://zakon.ru.xsph.ru/docx/parser/'+mask)  //ЗАКОН_О_НОТАРИАТЕ.docx
				//.get('http://127.0.0.1:8000/docx/parser/'+mask)  //ЗАКОН_О_НОТАРИАТЕ.docx
                .then(response => {
                    if (mask=="*"){
                        this.docfiles = response.data;
                        this.treejson = null;
                        this.treehtml = "";
                        //console.log(response.data);
                    }else{
                        this.treejson = response.data.json;
                        this.treehtml = response.data.html;
                        //console.log(response.data.json);
                    }
                    this.loading=false;
                    //console.log(response.data);
                })
                .catch(error => console.log(error));
            }            
            /*handleNodeClick(data) {
                console.log(data);
            }*/   
        }    
   }
</script>

<style scoped>
    .bg-purple-dark {
        background: #99a9bf;
    }
    .bg-purple {
        background: #d3dce6;
    }
    .bg-purple-light {
        background: #e5e9f2;
    }
    .grid-content {
        border-radius: 4px;
        min-height: 36px;
    }    
</style>