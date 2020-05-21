<template>
  <div class="firstPage">
      <div class="witeBg">
          <div class="ML_twenty F_S_T fontWeight PT_twenty PB_twenty">
              <span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;首页统计列表
              <!-- <span class="ML_twenty F_S_Sixteen">选择搜索方式:
                  <template>
                    <el-radio v-model="radio" label="1"  @change="selectFunction()">按月份或客户名称搜索</el-radio>
                    <el-radio v-model="radio" label="2"  @change="selectFunction()">按开始/结束时间或客户名称搜索</el-radio>
                  </template>
              </span> -->
              <span class="ML_twenty F_S_Sixteen">
                  <span class="bgButton" @click="goRankList()">DD单品排行</span>
                  <span class="bgButton ML_twenty" @click="clickSearch()">展开搜索</span>
              </span>
          </div>
          <div class="MT_Thirty ML_Thitty"  v-if="is_open">
            <!-- <template> -->
                <el-radio class="ML_Thitty" v-model="radio" label="1"  @change="selectFunction()">按月份搜索</el-radio><br/>
                <el-radio class="ML_Thitty" v-model="radio" label="2"  @change="selectFunction()">按开始/结束时间搜索</el-radio>
            <!-- </template> -->
            <span v-if="is_month" class="MT_Thirty">
                <span class="ML_Thitty">选择月份：</span>
                <el-date-picker v-model="month" type="month" @change="ifMonth()" value-format="yyyy-MM" placeholder="请选择月份"></el-date-picker>
            </span>
            <span v-if="is_time" class="MT_Thirty">
                <span class="ML_Thitty">开始时间：</span>
                <el-date-picker v-model="start_time" type="date" @change="ifTime()" value-format="yyyy-MM-dd" placeholder="请选择开始时间"></el-date-picker>
                <span class="ML_Thitty">结束时间：</span>
                <el-date-picker v-model="end_time" type="date" @change="ifTime()" value-format="yyyy-MM-dd" placeholder="请选择结束时间"></el-date-picker>
            </span>
            <div class="MT_Thirty PB_twenty">
                <span class="ML_Thitty">客户名称：</span>
                <template>
                        <el-select v-model="su_info" clearable placeholder="选择客户名称">
                            <el-option v-for="item in su_infoL" :key="item.id" :label="item.user_name" :value="item.id">
                            </el-option>
                        </el-select>
                </template>
                <span class="orangeButton ML_twenty" @click="searchFrame()">&nbsp;&nbsp;&nbsp;搜索&nbsp;&nbsp;&nbsp;</span>
            </div>
          </div>
      </div>
      <div v-show="!isShow" class="witeBg MT_Thirty PB_Thirty">
          <!-- <div class="ML_Four fontWeight F_S_T PT_twenty MB_twenty">需求</div> -->
          <div class="ML_Four MR_Four fontCenter F_S_0 PT_twenty">
              <span class="d_I_B border F_S_Sixteen shicaiListStyle">
                  <span class="F_S_twenty fontWeight">{{tableData.demand_num}}</span><br><span class="G_B_F">需求单总数</span>
              </span>
              <span class="d_I_B border F_S_Sixteen shicaiListStyle">
                  <span class="F_S_twenty fontWeight">{{tableData.sku_num}}</span><br><span class="G_B_F">子单SKU总数</span>
              </span>
              <span class="d_I_B border F_S_Sixteen shicaiListStyle">
                  <span class="F_S_twenty fontWeight">${{tableData.total_sale_price}}</span><br><span class="G_B_F">子单销售总额
                      <template>
                        <el-popover  placement="top-start" width="350" trigger="click">
                            <p>{{`${this.$ZDXSZENuma}`}}<br/>{{`${this.$ZDXSZENumb}`}}</p>
                            <i class="el-icon-question redFont" slot="reference"></i>
                        </el-popover>
                      </template>
                  </span>
              </span>
              <span class="d_I_B border F_S_Sixteen shicaiListStyle">
                  <span class="F_S_twenty fontWeight">${{tableData.total_discout_price}}</span><br><span class="G_B_F">销售毛利总额
                      <template>
                        <el-popover  placement="top-start" width="350" trigger="click">
                            <p>{{`${this.$XSMLZEa}`}}<br/>{{`${this.$XSMLZEb}`}}<br/>{{`${this.$XSMLZEc}`}}<br/>{{`${this.$XSMLZEd}`}}</p>
                            <i class="el-icon-question redFont" slot="reference"></i>
                        </el-popover>
                      </template>
                  </span>
              </span>
              <span class="d_I_B border F_S_Sixteen shicaiListStyle">
                  <span class="F_S_twenty fontWeight">${{tableData.total_sort_dis_price}}</span><br><span class="G_B_F">实采毛利总额
                      <template>
                        <el-popover  placement="top-start" width="350" trigger="click">
                            <p>{{`${this.$SCMLZEa}`}}<br/>{{`${this.$SCMLZEb}`}}<br/>{{`${this.$SCMLZEc}`}}</p>
                            <i class="el-icon-question redFont" slot="reference"></i>
                        </el-popover>
                      </template>
                  </span>
              </span>
              <span class="d_I_B border F_S_Sixteen shicaiListStyle">
                  <span class="F_S_twenty fontWeight G_O_F">8%</span><br><span class="G_B_F">标准毛利率
                      <template>
                        <el-popover  placement="top-start" width="350" trigger="click">
                            <p>{{`${this.$BZMLL}`}}</p>
                            <i class="el-icon-question redFont" slot="reference"></i>
                        </el-popover>
                      </template>
                  </span>
              </span>
              <!-- <span class="d_I_B border F_S_Sixteen shicaiListStyle">
              </span> -->
          </div>
          <div class="ML_Four MR_Four fontCenter F_S_0 MB_twenty">
              <span class="d_I_B border F_S_Sixteen shicaiListStyle">
                  <span class="F_S_twenty fontWeight G_O_F">{{tableData.total_sale_margin_rate}}</span><br><span class="G_B_F">报价逻辑毛利率
                      <template>
                        <el-popover  placement="top-start" width="350" trigger="click">
                            <p>{{`${this.$BJLJMLL}`}}</p>
                            <i class="el-icon-question redFont" slot="reference"></i>
                        </el-popover>
                      </template>
                  </span>
              </span>
              <span class="d_I_B border F_S_Sixteen shicaiListStyle">
                  <span class="F_S_twenty fontWeight G_O_F">{{tableData.total_sort_margin_rate}}</span><br><span class="G_B_F">实采毛利率
                      <template>
                        <el-popover  placement="top-start" width="350" trigger="click">
                            <p>{{`${this.$SCMLLa}`}}<br/>{{`${this.$SCMLLb}`}}</p>
                            <i class="el-icon-question redFont" slot="reference"></i>
                        </el-popover>
                      </template>
                  </span>
              </span>
              <span class="d_I_B border F_S_Sixteen shicaiListStyle" style="padding-top: 59px;height: 63px;">
                  <span class="F_S_twenty fontWeight G_O_F"></span><br><span class="G_B_F"></span>
              </span>
              <span class="d_I_B border F_S_Sixteen shicaiListStyle" style="padding-top: 59px;height: 63px;">
                  <span class="F_S_twenty fontWeight G_O_F"></span><br><span class="G_B_F"></span>
              </span>
              <span class="d_I_B border F_S_Sixteen shicaiListStyle" style="padding-top: 59px;height: 63px;">
                  <span class="F_S_twenty fontWeight G_O_F"></span><br><span class="G_B_F"></span>
              </span>
              <span class="d_I_B border F_S_Sixteen shicaiListStyle" style="padding-top: 59px;height: 63px;">
                  <span class="F_S_twenty fontWeight G_O_F"></span><br><span class="G_B_F"></span>
              </span>
              <!-- <span class="d_I_B border F_S_Sixteen shicaiListStyle" style="padding-top: 59px;height: 63px;">
                  <span class="F_S_twenty fontWeight G_O_F"></span><br><span class="G_B_F"></span>
              </span> -->
          </div>
          <!-- <div class="ML_Four fontWeight F_S_T PT_twenty MB_twenty">实采</div> -->
          <div class="ML_Four MR_Four fontCenter F_S_0">
              <span class="d_I_B border F_S_Sixteen shicaiListStyle">
                  <span class="F_S_twenty fontWeight">{{tableData.demand_goods_num}}</span><br><span class="G_B_F">需求商品总数
                      <template>
                        <el-popover  placement="top-start" width="350" trigger="click">
                            <p>{{`${this.$XQSPZS}`}}</p>
                            <i class="el-icon-question redFont" slot="reference"></i>
                        </el-popover>
                      </template>
                  </span>
              </span>
              <span class="d_I_B border F_S_Sixteen shicaiListStyle">
                  <span class="F_S_twenty fontWeight">${{tableData.total_pur_price}}</span><br><span class="G_B_F">需求商品总额
                      <template>
                        <el-popover  placement="top-start" width="350" trigger="click">
                            <p>{{`${this.$XQSPZE}`}}</p>
                            <i class="el-icon-question redFont" slot="reference"></i>
                        </el-popover>
                      </template>
                  </span>
              </span>
              <span class="d_I_B border F_S_Sixteen shicaiListStyle">
                  <span class="F_S_twenty fontWeight">{{tableData.demand_sort_num}}</span><br><span class="G_B_F">需求单总分货数</span>
              </span>
              <span class="d_I_B border F_S_Sixteen shicaiListStyle">
                  <span class="F_S_twenty fontWeight">${{tableData.total_sort_price}}</span><br><span class="G_B_F">实采总额
                      <template>
                        <el-popover  placement="top-start" width="350" trigger="click">
                            <p>{{`${this.$SCZEa}`}}<br/>{{`${this.$SCZEb}`}}<br/>{{`${this.$SCZEc}`}}<br/>{{`${this.$SCZEd}`}}</p>
                            <i class="el-icon-question redFont" slot="reference"></i>
                        </el-popover>
                      </template>
                  </span>
              </span>
              <span class="d_I_B border F_S_Sixteen shicaiListStyle">
                  <span class="F_S_twenty fontWeight G_O_F">{{tableData.sort_real_rate}}</span><br><span class="G_B_F">需求满足率
                      <template>
                        <el-popover  placement="top-start" width="350" trigger="click">
                            <p>{{`${this.$XQMZL}`}}</p>
                            <i class="el-icon-question redFont" slot="reference"></i>
                        </el-popover>
                      </template>
                  </span>
              </span>
              <span class="d_I_B border F_S_Sixteen shicaiListStyle">
                  <span class="F_S_twenty fontWeight G_O_F">{{tableData.total_price_margin_rate}}</span><br><span class="G_B_F">需求金额满足率
                      <template>
                        <el-popover  placement="top-start" width="350" trigger="click">
                            <p>{{`${this.$XQJEMZL}`}}</p>
                            <i class="el-icon-question redFont" slot="reference"></i>
                        </el-popover>
                      </template>
                  </span>
              </span>
          </div>
          <div class="ML_Four MR_Four fontCenter F_S_0 MB_twenty">
              <span class="d_I_B border F_S_Sixteen shicaiListStyle">
                  <span class="F_S_twenty fontWeight">{{tableData.demand_diff_num}}</span><br><span class="G_B_F">缺口总数
                      <template>
                        <el-popover  placement="top-start" width="350" trigger="click">
                            <p>{{`${this.$QKZS}`}}</p>
                            <i class="el-icon-question redFont" slot="reference"></i>
                        </el-popover>
                      </template>
                  </span>
              </span>
              <span class="d_I_B border F_S_Sixteen shicaiListStyle">
                  <span class="F_S_twenty fontWeight">${{tableData.total_diff_price}}</span><br><span class="G_B_F">缺口总额
                      <template>
                        <el-popover  placement="top-start" width="350" trigger="click">
                            <p>{{`${this.$QKZE}`}}</p>
                            <i class="el-icon-question redFont" slot="reference"></i>
                        </el-popover>
                      </template>
                  </span>
              </span>
              <span class="d_I_B border F_S_Sixteen shicaiListStyle">
                  <span class="F_S_twenty fontWeight G_M_F">{{tableData.sort_diff_rate}}</span><br><span class="G_B_F">缺口率</span>
              </span>
              <span class="d_I_B border F_S_Sixteen shicaiListStyle">
                  <span class="F_S_twenty fontWeight">{{tableData.rpa_num}}</span><br><span class="G_B_F">待审核批次
                      <template>
                        <el-popover  placement="top-start" width="350" trigger="click">
                            <p>{{`${this.$DSHPC}`}}</p>
                            <i class="el-icon-question redFont" slot="reference"></i>
                        </el-popover>
                      </template>
                  </span>
              </span>
              <span class="d_I_B border F_S_Sixteen shicaiListStyle" style="padding-top: 59px;height: 63px;">
                  <span class="F_S_twenty fontWeight"></span><br><span class="G_B_F"></span>
              </span>
              <span class="d_I_B border F_S_Sixteen shicaiListStyle" style="padding-top: 59px;height: 63px;">
                  <span class="F_S_twenty fontWeight"></span><br><span class="G_B_F"></span>
              </span>
          </div>
          <div class="ML_Four fontWeight F_S_T PT_twenty MB_twenty">客户统计列表</div>
          <table class="ML_Four MR_Four fontCenter witeBg" style="width:95%;" border="0" cellspacing="0" cellpadding="0">
              <tr class="fontWeight">
                  <td>客户名称</td>
                  <td>需求数量</td>
                  <td>实采数量</td>
                  <td>客户销售毛利金额</td>
                  <td>客户销售金额</td>
                  <td>客户报价逻辑毛利率</td>
                  <td>客户实采毛利金额</td>
                  <td>客户实采销售金额</td>
                  <td>客户实采毛利率</td>
              </tr>
              <tr class="witeBg" v-for="item in sale_user_price">
                  <td class="widthOneFiveHundred">{{item.user_name}}</td>
                  <td style="color:#8693f3;" class="widthOneFiveHundred">
                      {{item.goods_num}}
                  </td>
                  <td style="color:#c48efe;" class="batchPriceSet widthOneFiveHundred">
                      {{item.sort_num}}
                  </td>
                  <td style="color:#ff9e37;" class="batchPriceDollar widthOneFiveHundred">
                      ${{item.sale_discount_price}}
                  </td>
                  <td style="color:#39c4ff;" class="batchPriceVIP widthOneFiveHundred">
                      ${{item.sale_price}}
                  </td>
                  <td style="color:#4fda97;" class="priceRate widthOneFiveHundred">
                      {{item.sale_margin_rate}}%
                    </td>
                  <td style="color:#f36e6f" class="disPrice widthOneFiveHundred">
                      ${{item.sort_discount_price}}
                    </td>
                  <td style="color:#fabc05" class="disRate widthOneFiveHundred">
                      ${{item.sort_sale_price}}
                  </td>
                  <td style="color:#61d713" class="disRate widthOneFiveHundred">{{item.sort_margin_rate}}%</td>
              </tr>
          </table>
          <div class="ML_Four fontWeight F_S_T PT_twenty MB_twenty">待返积分($)</div>
          <table v-if="listLength!=0" class="fontCenter ML_Four MR_Four witeBg" style="width:95%;" border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr class="witeBg">
                    <th></th>
                    <th v-for="item in tableData.integral_list_app">{{item.date}}</th>
                </tr>
            </thead>
            <tbody>
                <tr style="background-color:#5bc0ea;color:#fff;">
                    <td>总积分</td>
                    <td v-for="(key,value) in tableData.integral_list">{{key}}</td>
                </tr>
                <tr v-for="item in tableData.integral_total_app" class="witeBg">
                    <td>{{item.channels_name}}</td>
                    <td v-for="keys in item.info">{{keys.integral}}</td>
                </tr>
            </tbody>
          </table>
          <!-- <div class="ML_Four MR_Four" id="myChart" :style="{ height: '430px'}"></div> -->
          <div class="ML_Four fontWeight F_S_T PT_twenty MB_twenty">渠道实采统计表</div>
          <table class="ML_Four MR_Four fontCenter witeBg" style="width:95%;" border="0" cellspacing="0" cellpadding="0">
              <tr class="fontWeight witeBg">
                  <td>渠道名称</td>
                  <td>采购数量&nbsp;&nbsp;<img src="../image/icon_strip.png" class="Cursor channelTableIcon" @click="clickSwitch('day_buy_num','dayBuyNum_show')"/></td>
                  <td>立即结算&nbsp;&nbsp;<img src="../image/icon_strip.png" class="Cursor channelTableIcon" @click="clickSwitch('batch_price','batchPriceSet_show')"/></td>
                  <td>美金原价&nbsp;&nbsp;<img src="../image/icon_strip.png" class="Cursor channelTableIcon" @click="clickSwitch('batch_price','batchPriceDollar_show')"/></td>
                  <td>LVIP价格&nbsp;&nbsp;<img src="../image/icon_strip.png" class="Cursor channelTableIcon" @click="clickSwitch('batch_price','batchPriceVIP_show')"/></td>
                  <td>资金占比&nbsp;&nbsp;<img src="../image/icon_strip.png" class="Cursor channelTableIcon" @click="clickSwitch('price_rate','priceRate_show')"/></td>
                  <td>实采毛利额&nbsp;&nbsp;<img src="../image/icon_strip.png" class="Cursor channelTableIcon" @click="clickSwitch('dis_price','disPrice_show')"/></td>
                  <td>实采毛利率&nbsp;&nbsp;<img src="../image/icon_strip.png" class="Cursor channelTableIcon" @click="clickSwitch('dis_rate','disRate_show')"/></td>
                  <td>采购人员</td>
                  <td>最后上传时间
                      <span class="d_I_B Cursor" style="vertical-align: -9px;line-height: 10px;">
                        <i class="el-icon-caret-top" title="升序排列" @click="sortByDate('1','last_time')"></i><br/>
                        <i class="el-icon-caret-bottom" title="降序排列" @click="sortByDate('2','last_time')"></i>
                      </span>
                  </td>
              </tr>
              <!-- icon_numbe -->
              <tr class="witeBg" v-for="item in total_channel_list">
                    <td>{{item.channel_method_name}}</td>
                    <td style="color:#8693f3;" class="dayBuyNum">
                      <span v-if="dayBuyNum_show">{{item.day_buy_num}}</span>
                      <span v-if="!dayBuyNum_show" style="position: relative;">
                        <el-progress :text-inside="true" :stroke-width="18" :percentage="graphicSwitch(item.day_buy_num,'dayBuyNum_maxNum')"></el-progress>
                        <span class="rigthData">{{item.day_buy_num}}</span>
                      </span>
                    </td>
                    <td style="color:#c48efe;" class="batchPriceSet widthOneFiveHundred">
                      <span v-if="item.original_or_discount==2">
                          <span v-if="batchPriceSet_show">{{item.batch_price}}</span>
                          <span v-if="!batchPriceSet_show" style="position: relative;">
                            <el-progress :text-inside="true" :stroke-width="18" :percentage="graphicSwitch(item.batch_price,'batchPriceSet_maxNum')"></el-progress>
                            <span class="rigthData">{{item.batch_price}}</span>
                          </span>
                      </span>
                    </td>
                    <td style="color:#ff9e37;" class="batchPriceDollar widthOneFiveHundred">
                      <span v-if="item.original_or_discount==0">
                          <span v-if="batchPriceDollar_show">{{item.batch_price}}</span>
                          <span v-if="!batchPriceDollar_show" style="position: relative;">
                            <el-progress :text-inside="true" :stroke-width="18" :percentage="graphicSwitch(item.batch_price,'batchPriceDollar_maxNum')"></el-progress>
                            <span class="rigthData">{{item.batch_price}}</span>
                          </span>
                      </span>
                    </td>
                    <td style="color:#39c4ff;" class="batchPriceVIP widthOneFiveHundred">
                      <span v-if="item.original_or_discount==1">
                          <span v-if="batchPriceVIP_show">{{item.batch_price}}</span>
                          <span v-if="!batchPriceVIP_show" style="position: relative;">
                            <el-progress :text-inside="true" :stroke-width="18" :percentage="graphicSwitch(item.batch_price,'batchPriceVIP_maxNum')"></el-progress>
                            <span class="rigthData">{{item.batch_price}}</span>
                          </span>
                      </span>
                    </td>
                    <td style="color:#4fda97;" class="priceRate widthOneFiveHundred">
                      <span v-if="item.price_rate!=undefined">
                          <span v-if="priceRate_show">{{item.price_rate}}%</span>
                          <span v-if="!priceRate_show" style="position: relative;">
                            <el-progress :text-inside="true" :stroke-width="18" :percentage="graphicSwitch(item.price_rate,'priceRate_maxNum')"></el-progress>
                            <span class="rigthData">{{item.price_rate}}%</span>
                          </span>
                      </span>
                    </td>
                    <td style="color:#f36e6f" class="disPrice widthOneFiveHundred">
                      <span v-if="item.dis_price!=undefined">
                          <span v-if="disPrice_show">{{item.dis_price}}</span>
                          <span v-if="!disPrice_show" style="position: relative;">
                            <el-progress :text-inside="true" :stroke-width="18" :percentage="graphicSwitch(item.dis_price,'disPrice_maxNum')"></el-progress>
                            <span class="rigthData">{{item.dis_price}}</span>
                          </span>
                      </span>
                    </td>
                    <td style="color:#fabc05" class="disRate widthOneFiveHundred">
                      <span v-if="disRate_show">{{item.dis_rate}}%</span>
                      <span v-if="!disRate_show" style="position: relative;">
                        <el-progress :text-inside="true" :stroke-width="18" :percentage="graphicSwitch(item.dis_rate,'disRate_maxNum')"></el-progress>
                        <span class="rigthData">{{item.dis_rate}}%</span>
                      </span>
                    </td>
                  <td style="color:#61d713">{{item.real_name}}</td>
                  <td style="color:#508bef">{{item.last_time}}</td>
              </tr>
          </table>
          
      </div>
      <div v-show="!isShow" class="witeBg MT_Thirty w_60_ratio d_I_B">
          <div class="ML_Four fontWeight F_S_T PT_twenty MB_twenty">客户销售统计表</div>
          <div class="floatRight" id="userTable" :style="{ height: '510px',width:'100%'}"></div>
      </div>
      <div v-show="!isShow" class="witeBg MT_Thirty d_I_B floatRight" style="width:38%;">
          <div class="ML_Four fontWeight F_S_T PT_twenty MB_twenty">客户报价毛利表</div>
          <div class="floatRight" id="interestRate" :style="{ height: '510px',width:'100%'}"></div>
      </div>
      <div v-show="isShow" class="witeBg MT_Thirty PB_Thirty fontCenter">
        <!-- <notFound v-if="isShow"></notFound> -->
        <span class="notdata MT_twenty d_I_B"></span>
      </div>
      <div class="witeBg MT_Thirty">
          <div class="ML_Four fontWeight F_S_T PT_twenty MB_twenty">
              合单统计
              <span class="orangeButton floatRight MR_Four" @click="seeMore()">查看更多</span>
          </div>
          <div class="ML_Four MR_Four HDTJ PB_twenty">
              <div v-for="(item,index) in sd_list">
                <table class="MT_twenty B_T_L_R B_T_R_R B_B_R_R B_B_L_R" style="width:100%;" border="0" cellspacing="0" cellpadding="0">
                    <tr class="lineHeightNinety witeBg">
                        <td colspan="2" class="F_S_24 blueFont">
                            <span class="ML_twenty">{{item.sum_demand_name}}</span>
                        </td>
                        <td colspan="4" class="grayFont">
                            <div class="ML_twenty">合期单号：{{item.sum_demand_sn}}</div>
                            <div class="ML_twenty">创建时间：{{item.create_time}}</div>
                        </td>
                        <td class="PR B_T_R_R B_B_R_R" style="width:8%" rowspan="3">
                            <span class="PA_Seventeen blueFont F_S_T Cursor" style="right: 15px;">
                                <span @click="viewDetails(item.sum_demand_sn,index)">查看订单</span>
                                <img @click="viewDetails(item.sum_demand_sn,index)" :class="'viewImg'+index" style="vertical-align:-2px;" src="../image/check_down.png"/>
                            </span>
                        </td>
                    </tr>
                    <tr class="lineHeightNinety fontCenter witeBg">
                        <td style="width:13%">
                            <div class="F_S_24" style="line-height: 8px;margin-top: 18px;">{{item.sku_num}}</div>
                            <div class="grayFont">sku数</div>
                        </td>
                        <td style="width:13%">
                            <div class="F_S_24" style="line-height: 8px;margin-top: 18px;">{{item.goods_num}}</div>
                            <div class="grayFont">总需求数</div>
                        </td>
                        <td style="width:13%">
                            <div class="F_S_24" style="line-height: 8px;margin-top: 18px;">
                                <span v-if="item.may_num!=null">{{item.may_num}}</span>
                                <span v-else>-</span>
                            </div>
                            <div class="grayFont">可采数</div>
                        </td>
                        <td style="width:13%">
                            <div class="F_S_24" style="line-height: 8px;margin-top: 18px;">{{item.real_num}}</div>
                            <div class="grayFont">实采数</div>
                        </td>
                        <td style="width:13%">
                            <div class="F_S_24" style="line-height: 8px;margin-top: 18px;">{{item.diff_num}}</div>
                            <div class="grayFont">缺口数</div>
                        </td>
                        <td style="width:13%">
                            <div class="F_S_24" style="line-height: 8px;margin-top: 18px;">{{item.sort_num}}</div>
                            <div class="grayFont">待分货数量</div>
                        </td>
                    </tr>
                    <tr class="lineHeightNinety fontCenter witeBg">
                        <td style="width:13%">
                            <div class="F_S_24 G_M_F" style="line-height: 8px;margin-top: 18px;">{{item.demand_num}}</div>
                            <div class="grayFont">订单数</div>
                        </td>
                        <td style="width:13%">
                            <div class="F_S_24" style="line-height: 8px;margin-top: 18px;">{{item.total_purchase_price}}</div>
                            <div class="grayFont">总需求额</div>
                        </td>
                        <td style="width:13%">
                            <div class="F_S_24" style="line-height: 8px;margin-top: 18px;">{{item.diff_purchase_price}}</div>
                            <div class="grayFont">缺口额</div>
                        </td>
                        <td style="width:13%">
                            <div class="F_S_24 G_O_F" style="line-height: 8px;margin-top: 18px;">{{item.real_rate}}%</div>
                            <div class="grayFont">实采满足率</div>
                        </td>
                        <td style="width:13%">
                            <div class="F_S_24" style="line-height: 8px;margin-top: 18px;">{{item.status}}</div>
                            <div class="grayFont">状态</div>
                        </td>
                        <td style="width:13%">
                        </td>
                    </tr>
                </table>
                <div class="O_F_A w_ratio PR" v-if="index==indexnum">
                    <table class="fontCenter tabletitle" border="0" cellspacing="0" cellpadding="0" style="width:100%;border-top: 0px;">
                        <tr>
                            <td v-for="item in tabletitle">{{item}}</td>
                        </tr>
                        <tr v-for="orderInfo in tableDataTwo">
                            <td>{{orderInfo.demand_sn}}</td>
                            <td>{{orderInfo.external_sn}}</td>
                            <td>{{orderInfo.user_name}}</td>
                            <td>{{orderInfo.sale_user_account}}</td>
                            <td>{{orderInfo.expire_time}}</td>
                            <td>{{orderInfo.goods_num}}</td>
                            <td>{{orderInfo.sort}}</td>
                            <td>{{orderInfo.status}}</td>
                            <td>{{orderInfo.demand_type}}</td>
                        </tr>
                    </table>
                </div>
              </div>
          </div>
      </div>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
// import notFound from '@/components/UiAssemblyList/notFound';
import { uniq } from '@/filters/publicMethods.js'//引入有公用方法的jsfile 
// import fixedTable from '@/filters/fixed-table.js'
export default {
  components:{
    //   notFound,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      headersStr:'',
      tableData:[],
      total_channel_list:[],
      integral_list:[],
      integral_channel:[],
      integral_total:[],
      sd_list:[],
      isShow:false,
      start_time:'',
      end_time:'',
      month:'',
      total:0,//默认数据总数 
      pagesize:15,//每页条数默认15条 
      page:1,//page默认为1
      //合单统计
      sumDemandSnList:[],
      arrSpecSn:[],
      //展开商品 
      indexnum:-1,
      tableDataTwo:[],
      tabletitle:[],
      listLength:0,
      //渠道实采统计表切换 
      maxNum:0,
      dayBuyNum_show:true,
      dayBuyNum_maxNum:0,
      batchPriceSet_show:true,
      batchPriceSet_maxNum:0,
      batchPriceDollar_show:true,
      batchPriceDollar_maxNum:0,
      batchPriceVIP_show:true,
      batchPriceVIP_maxNum:0,
      priceRate_show:true,
      priceRate_maxNum:0,
      disPrice_show:true,
      disPrice_maxNum:0,
      disRate_show:true,
      disRate_maxNum:0,

      sale_user_price:'',
      su_infoL:[],
      su_info:'',
      //修改首页搜索
      is_time:false,
      is_month:false,
      is_open:false,
      radio:'',
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.getDataList();
    this.getNoticeList();
    this.demandPurchaseTaskList();
    // this.drawHistogram() 
  },
  methods:{
    getDataList(){
        let vm = this;
        vm.loading = Loading.service({fullscreen: true, text: '拼命加载中....'});
        // $(".fixed-table-box").fixedTable(); 
        axios.post(vm.url+vm.$firstPageURL,
            {
                "start_time":vm.start_time,
                "end_time":vm.end_time,
                "month":vm.month,
                "page":vm.page,
                "pageSize":vm.pagesize,
                "sale_user_id":vm.su_info,
            },
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            if(res.data.code==1000){
                vm.tableData=res.data.data;
                vm.total_channel_list=vm.tableData.total_channel_info;
                vm.integral_list=res.data.data.integral_list;
                vm.integral_channel=res.data.data.integral_channel;
                vm.integral_total=res.data.data.integral_total;
                vm.sale_user_price=res.data.data.sale_user_price;
                vm.su_infoL=res.data.data.su_info;
                vm.listLength=vm.tableData.integral_list_app.length;
                vm.isShow=false;
                // vm.drawHistogram(); 
                vm.drawUsreTable();
            }else{
                vm.isShow=true;
                $(".notdata").html(res.data.msg)
                // vm.$message(res.data.msg);
            }
        }).catch(function (error) {
            vm.loading.close();
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    demandPurchaseTaskList(){
        let vm = this;
        axios.get(vm.url+vm.$demandPurchaseTaskListURL+"?page="+vm.page+"&page_size=5"+"&is_page=0",
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            if(res.data.code==1000){
                vm.sd_list=res.data.data;
            }else{
                vm.$message(res.data.msg);
            }
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    seeMore(){
        let vm = this
        vm.$router.push('/JointStatistics');
    },
    //展开商品 
    viewDetails(sum_demand_sn,index){
        let vm = this;
        vm.indexnum=index;
        vm.tableDataTwo=[];
        vm.tabletitle=[];
        $('.viewImg'+index).attr('src',require('../image/check_up.png'));
        let content_text_height=$(event.target).parent().parent().parent().parent().parent().height();
        if(content_text_height<=285){
            let load = Loading.service({fullscreen: true, text: '拼命加载中....'});
            axios.get(vm.url+vm.$sumDemandInfoURL+"?sum_demand_sn="+sum_demand_sn,
                {
                    headers:vm.headersStr,
                },
            ).then(function(res){
                load.close();
                $('.viewImg'+index).attr('src',require('../image/check_down.png'));
                if(res.data.code=='1000'){
                    vm.tableDataTwo=res.data.data;
                    vm.tabletitle=['需求单号','外部订单号','客户名称','客户分组','交期','需求数量','分货排序号','状态','类型']
                    $(".tabletitle").addClass("lineHeightForty");
                }else{
                    vm.$message(res.data.msg)
                }
            }).catch(function (error) {
                load.close();
                if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
        }else{
            vm.tableDataTwo=[];
            vm.tabletitle=[];
            $(".tabletitle").removeClass("lineHeightForty");
        }
        
    },
    scrollEvent(){
        var a=document.getElementById("cc").scrollTop;
        var b=document.getElementById("cc").scrollLeft;
        document.getElementById("hh").scrollLeft=b;
    },
    searchFrame(){
        let vm = this;
        vm.page=1;
        if(vm.start_time==null){
            vm.start_time='';
        }
        if(vm.end_time==null){
            vm.end_time='';
        }
        if(vm.month==null){
            vm.month='';
        }
        var dataStartTime = new Date(vm.start_time)
        var startTime = dataStartTime.getTime()
        var dataEndTime = new Date(vm.end_time)
        var endTime = dataEndTime.getTime()
        if(endTime<startTime){
            vm.$message('开始时间不能大于结束时间！');
            return false;
        }
        vm.getDataList();
    },
    ifTime(){
        let vm = this;
        if(vm.start_time!=''&&vm.start_time!=null){
            vm.month='';
        }
        if(vm.end_time!=''&&vm.end_time!=null){
            vm.month='';
        }
    },
    ifMonth(){
        let vm = this;
        if(vm.month!=''&&vm.month!=null){
            vm.end_time='';
            vm.start_time='';
        }
    },
    getNoticeList(){
        let vm = this;
        axios.post(vm.url+vm.$getLoggerListURL,
            {},
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            if(res.data.loggerList.total != 0){
                // vm.tableData = res.data.loggerList.data;
                let loggerList = [];
                for(let i = 0; i < 10; i++){
                  loggerList.push(res.data.loggerList.data[i]);
                }
                sessionStorage.setItem("loggerList",JSON.stringify(loggerList));
            }
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    drawUsreTable(){
        let vm = this;
        let myChart = this.$echarts.init(document.getElementById('userTable'))
        let interestRate = this.$echarts.init(document.getElementById('interestRate'))
        let userName = [];
        let maoli = [];
        let xiaoshou = [];
        let bjmaolilv = [];
        vm.tableData.sale_user_price.forEach(element=>{
            userName.push(element.user_name)
            maoli.push(element.sale_discount_price);
            xiaoshou.push(element.sale_price);
            bjmaolilv.push(element.sale_margin_rate);
        })
        myChart.clear();
        myChart.setOption({
            title : {
                // text: '评价对象按平台来源统计',
                // x:'left'
            },
            tooltip : {
                trigger: 'axis',
                axisPointer : {            // 坐标轴指示器，坐标轴触发有效
                    type : 'line'        // 默认为直线，可选为：'line' | 'shadow'
                }
            },
            legend: {
                data:['毛利额','销售额'],
            },
            // grid: {
            //     show:true,
            //     backgroundColor:'#ffffff',
            //     // left: '3%',
            //     // right: '4%',
            //     // bottom: '3%',
            //     // containLabel: true
            // },
            xAxis:{
                data : userName,
                splitLine: {
                    lineStyle: {
                        type: 'dashed',
                        color: '#e7e5e6'
                    }
                },
                axisLine:{
                    lineStyle:{
                        color:'#adadad'
                    }
                },
                // splitNumber: 20
            },
            yAxis : [
                {
                    type : 'value',
                    name: '单位（美金）',
                    axisLine:{
                        lineStyle:{
                            color:'#adadad'
                        }
                    },
                    splitLine: {
                        show: true,
                        lineStyle: {
                            type: 'dashed'
                        }
                    },
                }
            ],
            "dataZoom": [{
                "show": true,
                "height": 10,
                "xAxisIndex": [
                    0
                ],
                bottom: 20,
                "start": 10,
                "end": 80,
                handleIcon: 'path://M306.1,413c0,2.2-1.8,4-4,4h-59.8c-2.2,0-4-1.8-4-4V200.8c0-2.2,1.8-4,4-4h59.8c2.2,0,4,1.8,4,4V413z',
                handleSize: '110%',
                handleStyle:{
                    color:"#d3dee5",
                    
                },
                textStyle:{
                    color:"#fff"},
                borderColor:"#90979c"
                
                
            }, {
                "type": "inside",
                "show": true,
                "height": 15,
                "start": 1,
                "end": 35
            }],
            series : [
                {
                    name:'毛利额',
                    type:'bar',
                    barWidth: '15',
                    barGap:'2px',
                    itemStyle: {
                        normal: {
                                color: '#01cec7'
                            }
                    },
                    label: {
                        normal: {
                            show: true,
                            textStyle: {
                                color: '#01cec7'
                            },
                            position: 'top'
                        }
                    },
                    data:maoli
                },
                {
                    name:'销售额',
                    type:'bar',
                    barWidth: '15',
                    barGap:'2px',
                    itemStyle: {
                        normal: {
                                color: '#f99e35'
                            }
                    },
                    label: {
                        normal: {
                            show: true,
                            textStyle: {
                                color: '#f99e35'
                            },
                            position: 'top'
                        }
                    },
                    data:xiaoshou
                }
            ]
        })
        interestRate.clear();
        interestRate.setOption({
            title : {
                // text: '评价对象按平台来源统计',
                x:'left'
            },
            tooltip : {
                trigger: 'axis',
                axisPointer : {            // 坐标轴指示器，坐标轴触发有效
                    type : 'line'        // 默认为直线，可选为：'line' | 'shadow'
                }
            },
            legend: {
                data:['报价毛利率'],
            },
            grid: {
                show:true,
                backgroundColor:'#ffffff',
                // left: '3%',
                // right: '4%',
                // bottom: '3%',
                // containLabel: true
            },
            xAxis:{
                data : userName,
                splitLine: {
                    lineStyle: {
                        type: 'dashed',
                        color: '#e7e5e6'
                    }
                },
                axisLine:{
                    lineStyle:{
                        color:'#adadad'
                    }
                },
                splitNumber: 20
            },
            yAxis : [
                {
                    name: '比例(%)',
                    nameTextStyle: {fontSize: 14},
                    // min: 0,
                    // max: 100,
                    // splitLine: {show: false},
                    splitLine: {
                        show: true,
                        lineStyle: {
                            type: 'dashed'
                        }
                    },
                    axisLabel: {
                        show: true,
                        formatter: "{value} %", //右侧Y轴文字显示
                        // textStyle: {
                        //     color: "#ebf8ac"
                        // }
                    },
                    axisLine:{
                        lineStyle:{
                            color:'#adadad'
                        }
                    }
                }
            ],
            "dataZoom": [{
                "show": true,
                "height": 10,
                "xAxisIndex": [
                    0
                ],
                bottom: 20,
                "start": 10,
                "end": 80,
                handleIcon: 'path://M306.1,413c0,2.2-1.8,4-4,4h-59.8c-2.2,0-4-1.8-4-4V200.8c0-2.2,1.8-4,4-4h59.8c2.2,0,4,1.8,4,4V413z',
                handleSize: '110%',
                handleStyle:{
                    color:"#d3dee5",
                    
                },
                textStyle:{
                    color:"#fff"},
                borderColor:"#90979c"
                
                
            }, {
                "type": "inside",
                "show": true,
                "height": 15,
                "start": 1,
                "end": 35
            }],
            series : [
                {
                    name:'报价毛利率',
                    type:'bar',
                    barWidth: '15',
                    // barGap:'2px',
                    // yAxisIndex: 1,
                    itemStyle: {
                        normal: {
                                color: '#fc7b8d',
                                 label: {
                                    show: true,
                                    position: 'top',
                                    // formatter:"{bjmaolilv} %",
                                    formatter: function(params) {
                                        // bjmaolilv.forEach(function(value, index) {
                                        //     total = value;
                                        // });.toFixed(2)
                                        let percent = params.data
                                        return  percent + '%'; 
                                    },
                                }
                            },
                       
                    },
                    // label: {
                    //     normal: {
                    //         show: true,
                    //         textStyle: {
                    //             color: '#000'
                    //         },
                    //         position: 'top'
                    //     }
                    // },
                    data:bjmaolilv
                },
            ]
        })
    },
    //String和number类型的排序
    sortByDate(status,type){
        let vm = this;
        $(event.target).addClass('redFont')
        $(event.target).siblings().removeClass('redFont')
        let total_channel_list = vm.total_channel_list;
        function swap(total_channel_list, i, j){
            const temp = total_channel_list[i];
            total_channel_list[i] = total_channel_list[j];
            total_channel_list[j] = temp;
        }
        if(status=='2'){//降序 
            for(let i = 0; i < total_channel_list.length - 1; i++){
                let flag = false;
                for(let j = 0; j < total_channel_list.length - 1 - i; j++){
                    var timestamp1 = new Date(total_channel_list[j][type]).getTime();
                    var timestamp2 = new Date(total_channel_list[j+1][type]).getTime();
                    if(timestamp1 > timestamp2){
                        swap(total_channel_list, j, j+1);
                        flag = true;
                    }
                }
                if(!flag){
                    break;
                }
            }
            vm.total_channel_list=[];
            vm.total_channel_list = total_channel_list;
        }else if(status=='1'){//升序 
            for(let i = 0; i < total_channel_list.length - 1; i++){
                let flag = false;
                for(let j = 0; j < total_channel_list.length - 1 - i; j++){
                    var timestamp1 = new Date(total_channel_list[j][type]).getTime();
                    var timestamp2 = new Date(total_channel_list[j+1][type]).getTime();
                    if(timestamp1 < timestamp2){
                        swap(total_channel_list, j, j+1);
                        flag = true;
                    }
                }
                if(!flag){
                    break;
                }
            }
            vm.total_channel_list=[];
            vm.total_channel_list = total_channel_list;
        }
    },
    clickSwitch(type,staut){
        let vm = this;
        vm[staut]=!vm[staut];
        if(!vm[staut]){
            $(event.target).attr("src",require("../image/icon_numbe.png"))
        }else{
            $(event.target).attr("src",require("../image/icon_strip.png"))
        }
        if(staut=='dayBuyNum_show'){
            let dataList = [];
            vm.total_channel_list.forEach(element=>{
                dataList.push(element[type])
            })
            var max = Math.max.apply(null,dataList);
            vm.dayBuyNum_maxNum=max
        }
        if(staut=='batchPriceSet_show'){
            let dataList = [];
            vm.total_channel_list.forEach(element=>{
                if(element.original_or_discount==2){
                    dataList.push(element[type])
                }
            })
            var max = Math.max.apply(null,dataList);
            vm.batchPriceSet_maxNum=max
        }
        if(staut=='batchPriceDollar_show'){
            let dataList = [];
            vm.total_channel_list.forEach(element=>{
                if(element.original_or_discount==0){
                    dataList.push(element[type])
                }
            })
            var max = Math.max.apply(null,dataList);
            vm.batchPriceDollar_maxNum=max
        }
        if(staut=='batchPriceVIP_show'){
            let dataList = [];
            vm.total_channel_list.forEach(element=>{
                if(element.original_or_discount==1){
                    dataList.push(element[type])
                }
            })
            var max = Math.max.apply(null,dataList);
            vm.batchPriceVIP_maxNum=max
        }
        if(staut=='priceRate_show'){
            let dataList = [];
            vm.total_channel_list.forEach(element=>{
                dataList.push(element[type])
            })
            var max = Math.max.apply(null,dataList);
            vm.priceRate_maxNum=max
        }
        if(staut=='disPrice_show'){
            let dataList = [];
            vm.total_channel_list.forEach(element=>{
                dataList.push(element[type])
            })
            var max = Math.max.apply(null,dataList);
            vm.disPrice_maxNum=max
        }
        if(staut=='disRate_show'){
            let dataList = [];
            vm.total_channel_list.forEach(element=>{
                dataList.push(element[type])
            })
            var max = Math.max.apply(null,dataList);
            vm.disRate_maxNum=max
        } 
    },
    //选择搜索方式 
    selectFunction(){
        let vm = this;
        if(vm.radio=='1'){
            vm.is_month=true;
            vm.is_time=false;
        }else if(vm.radio=='2'){
            vm.is_time=true;
            vm.is_month=false;
        }
    },
    clickSearch(){
        let vm = this;
        vm.is_open=!vm.is_open;
        if(vm.is_open==true){
            $(event.target).html('收起筛选')
        }else if(vm.is_open==false){
            $(event.target).html('展开筛选')
        }
    },
    goRankList(){
        let vm = this;
        vm.$router.push('/ddGoodsRankList');
    }
   },
    computed:{
        tableWidth(){
            let vm = this;
            let pageWidth = $(".select").width();
            if(vm.total_channel_list.channel_name!=undefined){
                let tableWidth = vm.total_channel_list.channel_name.length*180;
                if(tableWidth<pageWidth){
                    tableWidth=pageWidth;
                }
                return "width:"+tableWidth+"px"
            }
        },
        graphicSwitch(){
            let vm = this;
            return function(num,type){
                let ratio = (num/vm[type]*100)
                return ratio
            }
        }
    }
}
</script>
<style>
.verticalBarT{
    display: inline-block;
    width:2px;
    height: 40px;
    background: #eef0f4;
}
.xuqiuListStyle{
    width: 14.1%;
    height: 93px;
    line-height: 30px;
    padding-top: 29px;
}
.shicaiListStyle{
    width: 16.5%;
    height: 93px;
    line-height: 30px;
    padding-top: 29px;
}
.el-progress-bar__outer {
    /* height: 6px!important; */
    border-radius: 0px!important;
    background-color: #fff!important;
    overflow: hidden!important;
    position: relative!important;
    vertical-align: middle!important;
}
.el-progress-bar__innerText {
    display: none!important;
}
.el-progress{
    width: 80%!important;
}
.el-progress-bar{
    vertical-align: 12px!important;
}
.rigthData{
    font-size: 10px;
    position: absolute;
    top: 9px;
    right: -58px;
}
.el-progress-bar__inner{
    border-radius: 0px!important;
}
.dayBuyNum .el-progress-bar__inner{
    background-color: #8693f3!important;
}
.batchPriceSet .el-progress-bar__inner{
    background-color: #c48efe!important;
}
.batchPriceDollar .el-progress-bar__inner{
    background-color: #ff9e37!important;
}
.batchPriceVIP .el-progress-bar__inner{
    background-color: #39c4ff!important;
}
.priceRate .el-progress-bar__inner{
    background-color: #4fda97!important;
}

.disPrice .el-progress-bar__inner{
    background-color: #f36e6f!important;
}
.disRate .el-progress-bar__inner{
    background-color: #fabc05!important;
}
.channelTableIcon{
    vertical-align: -2px;
    width: 16px;
    height: 16px;
}
.HDTJ table tr:hover{
    background-color: #fff!important;
}
.Select{
    width: 20px;
    height: 20px;
    opacity: 0;
    z-index: 9999;
    font-size: 100px;
    position: absolute;
    right: 43%;
    top: 37px;
    margin: auto;
}
.Select_img{
    position: absolute;
    right: 40%;
    top: 34px;
    z-index: 1;
    width: 26px;
    height: 26px;
}
.allSelect{
    width: 20px;
    height: 20px;
    opacity: 0;
    z-index: 9999;
    font-size: 100px;
    position: absolute;
    right: 47%;
    top: 3px;
    margin: auto;
}
.allSelect_img{
    position: absolute;
    right: 44px;
    top: 0px;
    z-index: 1;
    width: 26px;
    height: 26px;
}
</style>


<style scoped>
@import '../css/publicCss.css';
</style>
