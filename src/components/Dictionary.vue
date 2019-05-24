<template>
	<div class="container">
		<transition
						name="custom-classes-transition"
						enter-active-class="animated bounceInLeft"
		>
			<div class="table-select" v-if="tableSelectView">
				<div class="tab-nav">
					<div v-bind:class="[activeTabindex == 0 ? 'active' : '']" v-on:click="selectNavTab(0,$event)">表名
					</div>
					<div class="middle" v-bind:class="[activeTabindex == 1 ? 'active' : '']" v-on:click="selectNavTab(1,$event)">
						备注名
					</div>
					<div v-bind:class="[activeTabindex == 2 ? 'active' : '']" v-on:click="selectNavTab(2,$event)">点击记录</div>
				</div>
				<div class="tab-content">
					<ul v-bind:class="[activeTabindex == 0 ? 'show' : '']">
						<li class="copy" v-for="(value, key, index) in mysqldata.tables"
						    v-bind:class="[key == currentTableData.tablename ? 'active' : '']"
						    v-on:click="viewTable(key,$event)">
							<p><i>{{ index+1 }}</i>{{ key }}</p>
						</li>
					</ul>
					<ul v-bind:class="[activeTabindex == 1 ? 'show' : '']">
						<li v-for="(value, key, index) in mysqldata.tables"
						    v-bind:class="[key == currentTableData.tablename ? 'active' : '']"
						    v-on:click="viewTable(key,$event)">
							<p><i>{{ index+1 }}</i>{{ value.comment | defaultVal(key) }}</p></li>
					</ul>
					<ul v-bind:class="[activeTabindex == 2 ? 'show' : '']">
						<li v-for="(value, key, index) in clickHistory"
						    v-bind:class="[key == currentTableData.tablename ? 'active' : '']"
						    v-on:click="viewTable(key,$event)">
							<p><i>{{ index+1 }}</i>{{ key }} ({{ value.pv }})次</p></li>
						<li v-show="emptyHistory"><p><i>0</i>暂无记录</p></li>
					</ul>
				</div>
			</div>
		</transition>
		<transition
						name="custom-classes-transition"
						enter-active-class="animated bounceInRight"
						leave-active-class="animated bounceOutLeft"
		>
			<div class="table-list" v-show="tableListView">
				<div class="title">
					<h2>数据库字典</h2>
				</div>
				<div class="table-row">
					<div class="table">
						<table>
							<thead>
							<tr>
								<th>编号</th>
								<th width="30%">表名</th>
								<th>表类型</th>
								<th>备注</th>
							</tr>
							</thead>
							<tbody>
							<tr v-for="(value, key, index) in mysqldata.tables">
								<td>{{ index+1 }}</td>
								<td><p class="copy" v-on:click="viewTable(key,$event)">{{ key }}</p></td>
								<td>{{ value.engine }}</td>
								<td class="copy" v-on:click="CopyThat(value.comment,$event)">{{ value.comment }}</td>
							</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</transition>
		<transition
						name="custom-classes-transition"
						enter-active-class="animated bounceInRight"
						leave-active-class="animated bounceOutLeft"
		>
			<div class="table-desc" v-if="tableDescView">
				<div class="title">
					<h2 style="position: static">【{{currentTableData.tablename}}】{{currentTableData.info.comment}}</h2>
				</div>
        <div class="table-nav">
          <div :style="index==current?colorObj:''" v-for="(item,index) in tabNav" @click="tabClick(item,index)">{{item}}</div>
        </div>
				<div class="table-row">
					<div class="table" style="top: 10px;">
						<table v-show="tableShow=='字段信息'">
							<tbody>
							<tr>
								<th>编号</th>
								<th>字段名</th>
								<th>字段类型</th>
								<th>默认值</th>
								<th>备注</th>
							</tr>
							<tr v-for="item,index in currentTableData.fields">
								<td>{{ index+1 }}</td>
								<td class="copy" v-on:click="CopyThat(item.column_name,$event)">{{ item.column_name }}</td>
								<td>{{ item.column_type }}</td>
								<td>{{ item.column_default }}</td>
								<td class="copy"  v-on:click="CopyThat(item.column_comment,$event)">{{ item.column_comment }}</td>
							</tr>
							</tbody>
						</table>
            <table v-show="tableShow=='索引信息'">
              <tbody>
              <tr>
                <th>编号</th>
                <th>名称</th>
                <th>顺序</th>
                <th>字段</th>
                <th>类型</th>
                <th>方式</th>
                <th>备注</th>
              </tr>
              <tr v-for="item,index in IndexesData">
                <td>{{ index+1 }}</td>
                <td class="copy" v-on:click="CopyThat(item.key_name,$event)">{{ item.key_name }}</td>
                <td>{{ item.seq_in_index }}</td>
                <td class="copy" v-on:click="CopyThat(item.column_name,$event)">{{ item.column_name }}</td>
                <td>{{ item.non_unique==0?'Unique':'Normal'}}</td>
                <td>{{ item.index_type }}</td>
                <td>{{ item.index_comment }}</td>
              </tr>
              </tbody>
            </table>
					</div>
				</div>
			</div>
		</transition>
	</div>
</template>

<script type="text/ecmascript-6">
	import Clipboard from 'clipboard'
	export default {
		name: 'Dictionary',
		data () {
			return {
        colorObj:{
          color:'#00EE00',
        },
        current:0,
        tabNav:['字段信息','索引信息'],
				tableSelectView: false,
				activeTabindex: 0,
				mysqldata: {},
				currentTableData: {},
        IndexesData:{},
				clickHistory: {},
				emptyHistory: true,
				tableListView: false,
				tableDescView: false,
				summary: '',
        tableShow:'字段信息'
			}
		},
		methods: {
      tabClick(item,index){
        this.tableShow=item=='字段信息'?'字段信息':'索引信息'
        this.current=index
      },
			selectNavTab: function (i, event) {
				this.activeTabindex = i;
			},
			initCopy: function (target, val) {
				var clipboard = new Clipboard(target, {
					text: function () {
						return val;
					}
				});
				clipboard.on('success', function(e) {
					console.log('success');
					clipboard.destroy();
				});
				clipboard.on('error', function(e) {
					console.log('error');
					clipboard.destroy();
				});

			},
			CopyThat: function (val,event) {
				let app = this;
				app.initCopy(event.target, val);
			},
			viewTable: function (tablename, event) {
				let app = this;
				app.initCopy(event.target, tablename);
				if (app.currentTableData.tablename == tablename) {
					return false;
				}
				app.tableListView = false;
				app.tableDescView = false;

				if (app.emptyHistory) {
					app.emptyHistory = false;
				}
				if (app.clickHistory[tablename]) {
					app.clickHistory[tablename].pv += 1;
				} else {
					app.clickHistory[tablename] = app.mysqldata.tables[tablename];
					app.clickHistory[tablename].pv = 1;
				}
				app.viewTableFields(tablename);
			},
			viewTableFields: function (tablename) {
				let app = this;

				app.currentTableData.tablename = tablename;
				app.currentTableData.info = app.mysqldata.tables[tablename];
				app.currentTableData.fields = app.mysqldata.tableFields[tablename];
        app.IndexesData = app.mysqldata.tableIndexes[tablename];
				setTimeout(()=> {
					app.tableDescView = true;

				}, 20);
			},
			viewTableList: function () {
				let app = this;
				if (app.tableListView) {
					alert('已经是首页了');
					return false;
				}
				app.tableListView = true;
				app.tableDescView = false;
				app.currentTableData = {};
			},
			makeData: function () {
				let app = this;
				app.axios.get('data/initdata.php?t=' + (new Date()).valueOf()).then(function (response) {
					if (response.data instanceof Object == false) {
						alert('生成数据需要把项目部署到PHP环境中,用来读取数据库生成数据字典文件');
					} else {
						alert(response.data.message);
						window.location.reload();
					}
				}).catch(function (error) {
					console.log(error);
				});
			},
			getData: function () {

			}
		},
		filters: {
			defaultVal: function (value, defaultVal) {
				if (!value) return defaultVal;
				return value;
			}
		},
		beforeCreate: function () {
			let app = this;
			app.axios.get('data/data.json?t=' + (new Date()).valueOf()).then(function (response) {
				app.mysqldata = response.data;
				console.log(app.mysqldata);
			}).catch(function (error) {
				console.log(error);
			});

		},
		computed: {
			getClickHistory: function () {
				return this.clickHistory;
			}
		},
		mounted: function () {
			setTimeout(()=> {
				this.tableSelectView = true;
				this.tableListView = true;

			}, 20);
		}
	}
</script>
<style>
  .table-nav{
    display: flex;
    padding-bottom: 5px;
    border-bottom: 1px solid #ccc;
  }
  .table-nav div{
    margin-right: 20px;
    cursor: pointer;
    font-size: 14px;
  }
  .table-nav div:hover{
    color: #4f81bd;
  }
	.beta-bar {
		position: fixed;
		bottom: 0;
		right: 0px;
		padding: 8px 0;
		z-index: 999;
		width: 200px;
		background-color: #4f81bd;
		color: #fff;
		text-align: center;
		transform: translate3d(60px,-24px,0) rotate(-45deg);
	}
	.beta-bar a{
		color: #fff;
		text-decoration: none;
	}
</style>
