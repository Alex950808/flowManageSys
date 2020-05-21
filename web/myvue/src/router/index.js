import Vue from 'vue'
import Router from 'vue-router'
import Login from '@/components/Login'
import index from '@/components/index'
import headr from '@/components/headr'


//首页
// import indexPage from '@/components/indexPage'
import indexPage from '@/components/firstPage'
import sumDiffInfo from '@/components/sumDiffInfo'
import ddGoodsRankList from '@/components/ddGoodsRankList'
import JointStatistics from '@/components/JointStatistics'

//采购模块子页面
import procurementPeriod from '@/components/purchasingModule/procurementPeriod'
import purchasePeriodSetting from '@/components/purchasingModule/purchasePeriodSetting'
import procurementChannel from '@/components/purchasingModule/procurementChannel'
import procurementMethod from '@/components/purchasingModule/procurementMethod'
import costCoefficient from '@/components/purchasingModule/costCoefficient'
import auditDemand from '@/components/purchasingModule/auditDemand'
import auditDemandDetails from '@/components/purchasingModule/auditDemandDetails'
import excellentRecommend from '@/components/purchasingModule/excellentRecommend'
import excellentRecommendDetails from '@/components/purchasingModule/excellentRecommendDetails'
import upData from '@/components/purchasingModule/upData'
import confirmUpData from '@/components/purchasingModule/confirmUpData'
import expectedToArrive from '@/components/purchasingModule/expectedToArrive'
import increaseBatch from '@/components/purchasingModule/increaseBatch'
import confirmDifference from '@/components/purchasingModule/confirmDifference'
import differenceConfirmation from '@/components/purchasingModule/differenceConfirmation'
import overtimeDifference from '@/components/purchasingModule/overtimeDifference'
import stayOpenBill from '@/components/purchasingModule/stayOpenBill'
import confirmOpenBill from '@/components/purchasingModule/confirmOpenBill'
import timeoutProcessing from '@/components/purchasingModule/timeoutProcessing'
import purchaseSatisfaction from '@/components/purchasingModule/purchaseSatisfaction'
import commoditySatisfaction from '@/components/purchasingModule/commoditySatisfaction'
import purchaseID from '@/components/purchasingModule/purchaseID'
import batchDetails from '@/components/purchasingModule/batchDetails'
import uploadDiffData from '@/components/purchasingModule/uploadDiffData'
import batchSetting from '@/components/purchasingModule/batchSetting'
import purchaseOutList from '@/components/purchasingModule/purchaseOutList'
import waitAllotDemand from '@/components/purchasingModule/purchaseList'
import purchaseDemand from '@/components/purchasingModule/purchaseDemand'
import distributionPage from '@/components/purchasingModule/distributionPage'
import alreadyAllotDemand from '@/components/purchasingModule/alreadyAllotDemand'
import allocatedPage from '@/components/purchasingModule/allocatedPage'
import dpmDivisionGoods from '@/components/purchasingModule/dpmDivisionGoods'
import puremandManageDetails from '@/components/purchasingModule/puremandManageDetails'
import predictDemandList from '@/components/purchasingModule/predictDemandList'
import predictDetail from '@/components/purchasingModule/predictDetail'
import discountList from '@/components/purchasingModule/discountList'
import supplierList from '@/components/purchasingModule/supplierList'
import batchAuditList from '@/components/purchasingModule/batchAuditList'
import purchaseDemandList from '@/components/purchasingModule/purchaseDemandList'
import purchaseTaskList from '@/components/purchasingModule/purchaseTaskList'
import purchaseTaskDetail from '@/components/purchasingModule/purchaseTaskDetail'
import purchaseDemandDetail from '@/components/purchasingModule/purchaseDemandDetail'
import getCardConsumeList from '@/components/purchasingModule/getCardConsumeList'
import teamList from '@/components/purchasingModule/teamList'

//销售模块子页面
import demandManagement from '@/components/salesModule/demandManagement'
import demandForReporting from '@/components/salesModule/demandForReporting'
import pastDemand from '@/components/salesModule/pastDemand'
import distributionForecast from '@/components/salesModule/distributionForecast'
import seeData from '@/components/salesModule/seeData'
import realGoods from '@/components/salesModule/realGoods'
import goodsSeeData from '@/components/salesModule/goodsSeeData'
import commoditySalesRate from '@/components/salesModule/commoditySalesRate'
import viewDelivery from '@/components/salesModule/viewDelivery'
import timeoutOrder from '@/components/salesModule/timeoutOrder'
import seeCustomsClearance from '@/components/salesModule/seeCustomsClearance'
import overtime from '@/components/salesModule/overtime'
import demandManageDetails from '@/components/salesModule/demandManageDetails'
import realPurList from '@/components/salesModule/realPurList'
import distributionOfGoods from '@/components/salesModule/distributionOfGoods'
import purOrderList from '@/components/salesModule/PurOrderList'
import purDemOrdList from '@/components/salesModule/purDemOrdList'
import purDemOrdInfo from '@/components/salesModule/purDemOrdInfo'
import quotationDevice from '@/components/salesModule/quotationDevice'
import quotationDetails from '@/components/salesModule/quotationDetails'
import getChargeList from '@/components/salesModule/getChargeList'
import demandAllotList from '@/components/salesModule/demandAllotList'
import demandPurchaseList from '@/components/salesModule/demandPurchaseList'
import demandPurchaseDetail from '@/components/salesModule/demandPurchaseDetail'
import purRealList from '@/components/salesModule/purRealList'
import demandPassageList from '@/components/salesModule/demandPassageList'
import passageRealPurchaseList from '@/components/salesModule/passageRealPurchaseList'
import passageRealPurchaseDetail from '@/components/salesModule/passageRealPurchaseDetail'
import saleAccountList from '@/components/taskModule/saleAccountList'
import getMisOrderList from '@/components/salesModule/getMisOrderList'
import MISorderListDetails from '@/components/salesModule/MISorderListDetails'
import refundRulesManagement from '@/components/salesModule/refundRulesManagement'
import misSubOrderList from '@/components/salesModule/misSubOrderList'
import misCanDeliverGoods from '@/components/salesModule/misCanDeliverGoods'
import getSubList from '@/components/salesModule/getSubList'
import getSubDetail from '@/components/salesModule/getSubDetail'
import getSpotList from '@/components/salesModule/getSpotList'
import getSpotDetail from '@/components/salesModule/getSpotDetail'
import ordGoodsOffer from '@/components/salesModule/ordGoodsOffer'
import sellDeliverOrderList from '@/components/salesModule/sellDeliverOrderList'
import misDemandList from '@/components/salesModule/demandManagement'
import userSortData from '@/components/salesModule/userSortData'
import addorditSaleAccountUser from '@/components/taskModule/addorditSaleAccountUser'
import getOrderNewGoods from '@/components/salesModule/getOrderNewGoods'
import batchOrderList from '@/components/salesModule/batchOrderList'
import sumSortDataList from '@/components/salesModule/sumSortDataList'
import batchOrdSortData from '@/components/salesModule/batchOrdSortData'


//物流模块子页面
import pendingPurchaseOrder from '@/components/logisticsModule/pendingPurchaseOrder'
import clearPoint from '@/components/logisticsModule/clearPoint'
import timeoutCheck from '@/components/logisticsModule/timeoutCheck'
import purchaseOrder from '@/components/logisticsModule/purchaseOrder'
import clearTheWarehouse from '@/components/logisticsModule/clearTheWarehouse'
import outOfTime from '@/components/logisticsModule/outOfTime'
import pendingDeliveryOrder from '@/components/logisticsModule/pendingDeliveryOrder'
import timeoutUnshippedDelivery from '@/components/logisticsModule/timeoutUnshippedDelivery'
import pendingCustomsClearance from '@/components/logisticsModule/pendingCustomsClearance'
import timeoutClearance from '@/components/logisticsModule/timeoutClearance'
import pendingOrder from '@/components/logisticsModule/pendingOrder'
import timeoutTransfer from '@/components/logisticsModule/timeoutTransfer'
import distributionOrderList from '@/components/logisticsModule/distributionOrderList'
import distributionOrderDetail from '@/components/logisticsModule/distributionOrderDetail'
import sellReturnOrderList from '@/components/logisticsModule/sellReturnOrderList'
import sellReturnOrderDetail from '@/components/logisticsModule/sellReturnOrderDetail'


//权限模块
import permissiomList from '@/components/permissionModule/addPermissionManagement'
import addRole from '@/components/permissionModule/addRole'
import roleList from '@/components/permissionModule/roleList'
import adminUserList from '@/components/permissionModule/addAdminUser'
import modifyingTheRole from '@/components/permissionModule/modifyingTheRole'

//任务模块
import taskList from '@/components/taskModule/taskList'

//商品模块(任务模块和商品模块页面少，写在了一个文件中)
import commodity from '@/components/taskModule/commodity'
import goodsDetail from '@/components/taskModule/goodsDetail'
import editGoods from '@/components/taskModule/editGoods'
import saleUserList from '@/components/taskModule/saleUserList'
import userGoodsList from '@/components/taskModule/userGoodsList'
import userGoodsListByUp from '@/components/taskModule/userGoodsListByUp'
import commoditySpecification from '@/components/taskModule/commoditySpecification'
import getErpGoodsList from '@/components/commodityModule/getErpGoodsList'
import goodsLabelList from '@/components/commodityModule/goodsLabelList'
import standbyGoodsList from '@/components/taskModule/standbyGoodsList'
import offerList from '@/components/commodityModule/offerList'
import getOfferDetail from '@/components/commodityModule/getOfferDetail'
import ltGoodsList from '@/components/commodityModule/ltGoodsList'
import wholesaleList from '@/components/commodityModule/wholesaleList'
import getWholesaleDetail from '@/components/commodityModule/getWholesaleDetail'
import getWholeDetail from '@/components/commodityModule/getWholeDetail'



//汇总详情页面
import openDataCollection from '@/components/dataCollection/openDataCollection'
import summaryOfRequirements from '@/components/dataCollection/summaryOfRequirements'
import procurementSummary from '@/components/dataCollection/procurementSummary'
import batchSummary from '@/components/dataCollection/batchSummary'
import differenceSummary from '@/components/dataCollection/differenceSummary'
import demandSummary from '@/components/dataCollection/demandSummary'

//公告部分(公告部分同任务模块.商品模块写在同一个文件中)
import noticeList from '@/components/taskModule/addNotice'

//订单列表
import getOrderList from '@/components/taskModule/getOrderList'
import orderListDetails from '@/components/taskModule/orderListDetails'

//财务模块
import fundChannelList from '@/components/financialModule/fundChannelList'
import rongFundList from '@/components/financialModule/rongFundList'
import discretionaryFundList from '@/components/financialModule/discretionaryFundList'
import waitRefundlist from '@/components/financialModule/waitRefundlist'
import demandFundList from '@/components/financialModule/demandFundList'
import financeDeliverOrderList from '@/components/financialModule/financeDeliverOrderList'
import watiPricingBatchList from '@/components/financialModule/watiPricingBatchList'
import watiPricingBatchDetail from '@/components/financialModule/watiPricingBatchDetail'
import fundListDetail from '@/components/financialModule/fundListDetail'
import waitRefundDemandList from '@/components/financialModule/waitRefundDemandList'
import waitRefundDemandDetail from '@/components/financialModule/waitRefundDemandDetail'
import exchangeRateList from '@/components/financialModule/exchangeRateList'
import profitFormulaList from '@/components/financialModule/profitFormulaList'
import profitList from '@/components/financialModule/profitList'
import profitDetail from '@/components/financialModule/profitDetail'


//数据统计模块
import misOrderStatisticsList from '@/components/dataCollection/misOrderStatisticsList'
import demandStasticsList from '@/components/dataCollection/demandStasticsList'
import demandRealPurchaseList from '@/components/dataCollection/demandRealPurchaseList'
import demandPurchaseGoodsDetail from '@/components/dataCollection/demandPurchaseGoodsDetail'
import orderCurrentStatisticsList from '@/components/dataCollection/orderCurrentStatisticsList'
import orderEndStatisticsList from '@/components/dataCollection/orderEndStatisticsList'
import subOrderStatisticsList from '@/components/dataCollection/subOrderStatisticsList'
import goodsStatisticsList from '@/components/dataCollection/goodsStatisticsList'
import purchaseChannelStatisticsList from '@/components/dataCollection/purchaseChannelStatisticsList'
import purchaseChannelStatisticsDetail from '@/components/dataCollection/purchaseChannelStatisticsDetail'
import currentGoodsStatisticsList from '@/components/dataCollection/currentGoodsStatisticsList'

//系统设置
import marginRateList from '@/components/settingModule/marginRateList'
import purchaseDiscountList from '@/components/settingModule/purchaseDiscountList'
import accountList from '@/components/settingModule/accountList'
import waitIntegralList from '@/components/settingModule/waitIntegralList'
import waitIntegralDetail from '@/components/settingModule/waitIntegralDetail'
import discountTypeList from '@/components/settingModule/discountTypeList'
import getGmcDiscountList from '@/components/settingModule/getGmcDiscountList'
import DiscountTypeLogList from '@/components/settingModule/DiscountTypeLogList'
import downloadDiscountDiffGoods from '@/components/settingModule/downloadDiscountDiffGoods'
import brandDiscountList from '@/components/settingModule/brandDiscountList'
import ShopCartList from '@/components/settingModule/ShopCartList'
import downloadShopDiffGoods from '@/components/settingModule/downloadShopDiffGoods'
import targetList from '@/components/settingModule/targetList'
import goodsSaleList from '@/components/settingModule/goodsSaleList'
import downloadSaleDiffGoods from '@/components/settingModule/downloadSaleDiffGoods'

//日志模块
import getLoggerList from '@/components/JournalModule/getLoggerList'
import getLoggerDetail from '@/components/JournalModule/getLoggerDetail'
import getVersionList from '@/components/JournalModule/getVersionList'

//审核模块
import auditList from '@/components/auditModule/auditList'
import auditDetails from '@/components/auditModule/auditDetails'
import batchAuditDetail from '@/components/auditModule/batchAuditDetail'

//用户模块 
import userList from '@/components/userMosule/userList'
import classifyFieldList from '@/components/userMosule/classifyFieldList'



Vue.use(Router)

const router = new Router({
  routes: [
    {
      path: '/',
      name: 'Login',
      component: Login,
     
    },
    {
      path:'/index',
      name:"index",
      component:index,
      // meta:{  
      //   requireAuth:true //需要登录验证  
      // },
      children:[
        {
          path:'/procurementPeriod',
          name:'procurementPeriod',
          component:procurementPeriod
        },
        {
          path:'/purchasePeriodSetting',
          name:'purchasePeriodSetting',
          component:purchasePeriodSetting
        },
        {
          path:'/procurementChannel',
          name:'procurementChannel',
          component:procurementChannel
        },
        {
          path:'/procurementMethod',
          name:'procurementMethod',
          component:procurementMethod
        },
        {
          path:'/costCoefficient',
          name:'costCoefficient',
          component:costCoefficient
        },
        {
          path:'/auditDemand',
          name:'auditDemand',
          component:auditDemand,
        },
        {
          path:'/auditDemandDetails',
          name:'auditDemandDetails',
          component:auditDemandDetails
        }, 
        {
          path:'/excellentRecommend',
          name:'excellentRecommend',
          component:excellentRecommend
        },
        {
          path:'/excellentRecommendDetails',
          name:'excellentRecommendDetails',
          component:excellentRecommendDetails
        },
        {
          path:'/upData',
          name:'upData',
          component:upData
        },
        {
          path:'/confirmUpData',
          name:'confirmUpData',
          component:confirmUpData
        },
        {
          path:'/expectedToArrive',
          name:'expectedToArrive',
          component:expectedToArrive
        },
        {
          path:'/increaseBatch',
          name:'increaseBatch',
          component:increaseBatch
        },
        {
          path:'/confirmDifference',
          name:'confirmDifference',
          component:confirmDifference
        },
        {
          path:'/differenceConfirmation',
          name:'differenceConfirmation',
          component:differenceConfirmation
        },
        {
          path:'/overtimeDifference',
          name:'overtimeDifference',
          component:overtimeDifference
        },
        {
          path:'/stayOpenBill',
          name:'stayOpenBill',
          component:stayOpenBill
        },
        {
          path:'/confirmOpenBill',
          name:'confirmOpenBill',
          component:confirmOpenBill
        },
        {
          path:'/timeoutProcessing',
          name:'timeoutProcessing',
          component:timeoutProcessing
        },
        {
          path:'/purchaseSatisfaction',
          name:'purchaseSatisfaction',
          component:purchaseSatisfaction
        },
        {
          path:'/commoditySatisfaction',
          name:'commoditySatisfaction',
          component:commoditySatisfaction
        },
        {
          path:'/purchaseID',
          name:'purchaseID',
          component:purchaseID
        },
        {
          path:'/demandForReporting',
          name:'demandForReporting',
          component:demandForReporting
        },
        {
          path:'/pastDemand',
          name:'pastDemand',
          component:pastDemand
        },{
          path:'/distributionForecast',
          name:'distributionForecast',
          component:distributionForecast
        },
        {
          path:'/seeData',
          name:'seeData',
          component:seeData
        },
        {
          path:'/realGoods',
          name:'realGoods',
          component:realGoods
        },
        {
          path:'/goodsSeeData',
          name:'goodsSeeData',
          component:goodsSeeData
        },
        {
          path:'/commoditySalesRate',
          name:'commoditySalesRate',
          component:commoditySalesRate
        },
        {
          path:'/viewDelivery',
          name:'viewDelivery',
          component:viewDelivery
        },
        {
          path:'/timeoutOrder',
          name:'timeoutOrder',
          component:timeoutOrder
        },
        {
          path:'/seeCustomsClearance',
          name:'seeCustomsClearance',
          component:seeCustomsClearance
        },
        {
          path:'/overtime',
          name:'overtime',
          component:overtime
        },
        {
          path:'/demandManagement',
          name:'demandManagement',
          component:demandManagement
        },
        {
          path:'/pendingPurchaseOrder',
          name:'pendingPurchaseOrder',
          component:pendingPurchaseOrder
        },
        {
          path:'/clearPoint',
          name:'clearPoint',
          component:clearPoint
        },
        {
          path:'/timeoutCheck',
          name:'timeoutCheck',
          component:timeoutCheck
        },
        {
          path:'/purchaseOrder',
          name:'purchaseOrder',
          component:purchaseOrder
        },
        {
          path:'/clearTheWarehouse',
          name:'clearTheWarehouse',
          component:clearTheWarehouse
        },
        {
          path:'/outOfTime',
          name:'outOfTime',
          component:outOfTime
        },
        {
          path:'/pendingDeliveryOrder',
          name:'pendingDeliveryOrder',
          component:pendingDeliveryOrder
        },
        {
          path:'/timeoutUnshippedDelivery',
          name:'timeoutUnshippedDelivery',
          component:timeoutUnshippedDelivery
        },
        {
          path:'/pendingCustomsClearance',
          name:'pendingCustomsClearance',
          component:pendingCustomsClearance
        },
        {
          path:'/timeoutClearance',
          name:'timeoutClearance',
          component:timeoutClearance
        },
        {
          path:'/pendingOrder',
          name:'pendingOrder',
          component:pendingOrder
        },
        {
          path:'/timeoutTransfer',
          name:'timeoutTransfer',
          component:timeoutTransfer
        },
        {
          path:'/batchDetails',
          name:'batchDetails',
          component:batchDetails
        },
        {
          path:'/permissiomList',
          name:'permissiomList',
          component:permissiomList
        },
        {
          path:'/addRole',
          name:'addRole',
          component:addRole
        },
        {
          path:'/roleList',
          name:'roleList',
          component:roleList
        },
        {
          path:'/adminUserList',
          name:'adminUserList',
          component:adminUserList
        },
        {
          path:'/modifyingTheRole',
          name:'modifyingTheRole',
          component:modifyingTheRole
        },
        {
          path:'/openDataCollection',
          name:'openDataCollection',
          component:openDataCollection
        },
        {
          path:'/summaryOfRequirements',
          name:'summaryOfRequirements',
          component:summaryOfRequirements
        },
        {
          path:'/procurementSummary',
          name:'procurementSummary',
          component:procurementSummary
        },
        {
          path:'/batchSummary',
          name:'batchSummary',
          component:batchSummary
        },
        {
          path:'/differenceSummary',
          name:'differenceSummary',
          component:differenceSummary
        },
        {
          path:'/demandSummary',
          name:'demandSummary',
          component:demandSummary
        },
        {
          path:'/uploadDiffData',
          name:'uploadDiffData',
          component:uploadDiffData
        },
        {
          path:'/taskList',
          name:'taskList',
          component:taskList
        },
        {
          path:'/indexPage',
          name:'indexPage',
          component:indexPage
        },
        {
          path:'/commodity',
          name:'commodity',
          component:commodity
        },
        {
          path:'/goodsDetail',
          name:'goodsDetail',
          component:goodsDetail
        },
        {
          path:'/batchSetting',
          name:'batchSetting',
          component:batchSetting
        },
        {
          path:'/editGoods',
          name:'editGoods',
          component:editGoods
        },
        {
          path:'/noticeList',
          name:'noticeList',
          component:noticeList
        },
        {
          path:'/purchaseOutList',
          name:'purchaseOutList',
          component:purchaseOutList
        },
        {
          path:'/waitAllotDemand',
          name:'waitAllotDemand',
          component:waitAllotDemand
        },
        {
          path:'/purchaseDemand',
          name:'purchaseDemand',
          component:purchaseDemand
        },
        {
          path:'/distributionPage',
          name:'distributionPage',
          component:distributionPage
        },
        {
          path:'/alreadyAllotDemand',
          name:'alreadyAllotDemand',
          component:alreadyAllotDemand
        },
        {
          path:'/allocatedPage',
          name:'allocatedPage',
          component:allocatedPage
        },
        {
          path:'/demandManageDetails',
          name:'demandManageDetails',
          component:demandManageDetails
        },
        {
          path:'/realPurList',
          name:'realPurList',
          component:realPurList
        },
        {
          path:'/distributionOfGoods',
          name:'distributionOfGoods',
          component:distributionOfGoods
        },
        {
          path:'/purOrderList',
          name:'purOrderList',
          component:purOrderList
        },
        {
          path:'/purDemOrdList',
          name:'purDemOrdList',
          component:purDemOrdList
        },
        {
          path:'/purDemOrdInfo',
          name:'purDemOrdInfo',
          component:purDemOrdInfo
        },
        {
          path:'/quotationDevice',
          name:'quotationDevice',
          component:quotationDevice
        },
        {
          path:'/quotationDetails',
          name:'quotationDetails',
          component:quotationDetails
        },
        {
          path:'/getChargeList',
          name:'getChargeList',
          component:getChargeList
        },
        {
          path:'/demandAllotList',
          name:'demandAllotList',
          component:demandAllotList
        },
        {
          path:'/demandPurchaseList',
          name:'demandPurchaseList',
          component:demandPurchaseList
        },
        {
          path:'/demandPurchaseDetail',
          name:'demandPurchaseDetail',
          component:demandPurchaseDetail
        },
        {
          path:'/dpmDivisionGoods',
          name:'dpmDivisionGoods',
          component:dpmDivisionGoods
        },
        {
          path:'/puremandManageDetails',
          name:'puremandManageDetails',
          component:puremandManageDetails
        },
        {
          path:'/purRealList',
          name:'purRealList',
          component:purRealList
        },
        {
          path:'/saleUserList',
          name:'saleUserList',
          component:saleUserList
        },
        {
          path:'/userGoodsList',
          name:'userGoodsList',
          component:userGoodsList
        },
        {
          path:'/userGoodsListByUp',
          name:'userGoodsListByUp',
          component:userGoodsListByUp
        },
        {
          path:'/getOrderList',
          name:'getOrderList',
          component:getOrderList
        },
        {
          path:'/orderListDetails',
          name:'orderListDetails',
          component:orderListDetails,
        },
        {
          path:'/commoditySpecification',
          name:'commoditySpecification',
          component:commoditySpecification,
        },
        {
          path:'/demandPassageList',
          name:'demandPassageList',
          component:demandPassageList,
        },
        {
          path:'/passageRealPurchaseList',
          name:'passageRealPurchaseList',
          component:passageRealPurchaseList,
        },
        {
          path:'/passageRealPurchaseDetail',
          name:'passageRealPurchaseDetail',
          component:passageRealPurchaseDetail,
        },
        {
          path:'/fundChannelList',
          name:'fundChannelList',
          component:fundChannelList,
        },
        {
          path:'/saleAccountList',
          name:'saleAccountList',
          component:saleAccountList,
        },
        {
          path:'/rongFundList',
          name:'rongFundList',
          component:rongFundList,
        },
        {
          path:'/discretionaryFundList',
          name:'discretionaryFundList',
          component:discretionaryFundList,
        },
        {
          path:'/predictDemandList',
          name:'predictDemandList',
          component:predictDemandList,
        },
        {
          path:'/predictDetail',
          name:'predictDetail',
          component:predictDetail,
        },
        {
          path:'/getMisOrderList',
          name:'getMisOrderList',
          component:getMisOrderList,
        },
        {
          path:'/MISorderListDetails',
          name:'MISorderListDetails',
          component:MISorderListDetails,
        },
        {
          path:'/refundRulesManagement',
          name:'refundRulesManagement',
          component:refundRulesManagement,
        },
        {
          path:'/misSubOrderList',
          name:'misSubOrderList',
          component:misSubOrderList,
        },
        {
          path:'/misCanDeliverGoods',
          name:'misCanDeliverGoods',
          component:misCanDeliverGoods,
        },
        {
          path:'/distributionOrderList',
          name:'distributionOrderList',
          component:distributionOrderList,
        },
        {
          path:'/distributionOrderDetail',
          name:'distributionOrderDetail',
          component:distributionOrderDetail
        },
        {
          path:'/waitRefundlist',
          name:'waitRefundlist',
          component:waitRefundlist,
        },
        {
          path:'/demandFundList',
          name:'demandFundList',
          component:demandFundList,
        },
        {
          path:'/getSubList',
          name:'getSubList',
          component:getSubList,
        },
        {
          path:'/getSubDetail',
          name:'getSubDetail',
          component:getSubDetail,
        },
        {
          path:'/getSpotList',
          name:'getSpotList',
          component:getSpotList,
        },
        {
          path:'/getSpotDetail',
          name:'getSpotDetail',
          component:getSpotDetail,
        },
        {
          path:'/ordGoodsOffer',
          name:'ordGoodsOffer',
          component:ordGoodsOffer,
        },
        {
          path:'/financeDeliverOrderList',
          name:'financeDeliverOrderList',
          component:financeDeliverOrderList,
        },
        {
          path:'/discountList',
          name:'discountList',
          component:discountList,
        },
        {
          path:'/sellDeliverOrderList',
          name:'sellDeliverOrderList',
          component:sellDeliverOrderList,
        },
        {
          path:'/misDemandList',
          name:'misDemandList',
          component:misDemandList,
        },
        {
          path:'/sellReturnOrderList',
          name:'sellReturnOrderList',
          component:sellReturnOrderList,
        },
        {
          path:'/sellReturnOrderDetail',
          name:'sellReturnOrderDetail',
          component:sellReturnOrderDetail,
        },
        {
          path:'/watiPricingBatchList',
          name:'watiPricingBatchList',
          component:watiPricingBatchList,
        },
        {
          path:'/watiPricingBatchDetail',
          name:'watiPricingBatchDetail',
          component:watiPricingBatchDetail,
        },
        {
          path:'/supplierList',
          name:'supplierList',
          component:supplierList,
        },
        {
          path:'/misOrderStatisticsList',
          name:'misOrderStatisticsList',
          component:misOrderStatisticsList,
        },
        {
          path:'/demandStasticsList',
          name:'demandStasticsList',
          component:demandStasticsList,
        },
        {
          path:'/demandRealPurchaseList',
          name:'demandRealPurchaseList',
          component:demandRealPurchaseList,
        },
        {
          path:'/demandPurchaseGoodsDetail',
          name:'demandPurchaseGoodsDetail',
          component:demandPurchaseGoodsDetail,
        },
        {
          path:'/getErpGoodsList',
          name:'getErpGoodsList',
          component:getErpGoodsList,
        },
        {
          path:'/fundListDetail',
          name:'fundListDetail',
          component:fundListDetail,
        },
        {
          path:'/waitRefundDemandList',
          name:'waitRefundDemandList',
          component:waitRefundDemandList,
        },
        {
          path:'/waitRefundDemandDetail',
          name:'waitRefundDemandDetail',
          component:waitRefundDemandDetail,
        },
        {
          path:'/goodsLabelList',
          name:'goodsLabelList',
          component:goodsLabelList,
        },
        {
          path:'/userSortData',
          name:'userSortData',
          component:userSortData,
        },
        {
          path:'/orderCurrentStatisticsList',
          name:'orderCurrentStatisticsList',
          component:orderCurrentStatisticsList,
        },
        {
          path:'/orderEndStatisticsList',
          name:'orderEndStatisticsList',
          component:orderEndStatisticsList,
        },
        {
          path:'/subOrderStatisticsList',
          name:'subOrderStatisticsList',
          component:subOrderStatisticsList,
        },
        {
          path:'/goodsStatisticsList',
          name:'goodsStatisticsList',
          component:goodsStatisticsList,
        },
        {
          path:'/addorditSaleAccountUser',
          name:'addorditSaleAccountUser',
          component:addorditSaleAccountUser,
        },
        {
          path:'/marginRateList',
          name:'marginRateList',
          component:marginRateList,
        },
        {
          path:'/purchaseChannelStatisticsList',
          name:'purchaseChannelStatisticsList',
          component:purchaseChannelStatisticsList,
        },
        {
          path:'/purchaseChannelStatisticsDetail',
          name:'purchaseChannelStatisticsDetail',
          component:purchaseChannelStatisticsDetail,
        },
        {
          path:'/purchaseDiscountList',
          name:'purchaseDiscountList',
          component:purchaseDiscountList,
        },
        {
          path:'/getLoggerList',
          name:'getLoggerList',
          component:getLoggerList,
        },
        {
          path:'/getLoggerDetail',
          name:'getLoggerDetail',
          component:getLoggerDetail,
        },
        {
          path:'/batchAuditList',
          name:'batchAuditList',
          component:batchAuditList,
        },
        {
          path:'/auditList',
          name:'auditList',
          component:auditList,
        },
        {
          path:'/auditDetails',
          name:'auditDetails',
          component:auditDetails,
        },
        {
          path:'/currentGoodsStatisticsList',
          name:'currentGoodsStatisticsList',
          component:currentGoodsStatisticsList,
        },
        {
          path:'/batchAuditDetail',
          name:'batchAuditDetail',
          component:batchAuditDetail,
        },
        {
          path:'/getOrderNewGoods',
          name:'getOrderNewGoods',
          component:getOrderNewGoods,
        },
        {
          path:'/accountList',
          name:'accountList',
          component:accountList,
        },
        {
          path:'/waitIntegralList',
          name:'waitIntegralList',
          component:waitIntegralList,
        },
        {
          path:'/waitIntegralDetail',
          name:'waitIntegralDetail',
          component:waitIntegralDetail,
        },
        {
          path:'/discountTypeList',
          name:'discountTypeList',
          component:discountTypeList,
        },
        {
          path:'/purchaseDemandList',
          name:'purchaseDemandList',
          component:purchaseDemandList,
        },
        {
          path:'/purchaseTaskList',
          name:'purchaseTaskList',
          component:purchaseTaskList,
        },
        {
          path:'/purchaseTaskDetail',
          name:'purchaseTaskDetail',
          component:purchaseTaskDetail,
        },
        {
          path:'/getGmcDiscountList',
          name:'getGmcDiscountList',
          component:getGmcDiscountList,
        },
        {
          path:'/purchaseDemandDetail',
          name:'purchaseDemandDetail',
          component:purchaseDemandDetail,
        },
        {
          path:'/batchOrderList',
          name:'batchOrderList',
          component:batchOrderList,
        },
        {
          path:'/sumSortDataList',
          name:'sumSortDataList',
          component:sumSortDataList,
        },
        {
          path:'/batchOrdSortData',
          name:'batchOrdSortData',
          component:batchOrdSortData,
        },
        {
          path:'/sumDiffInfo',
          name:'sumDiffInfo',
          component:sumDiffInfo,
        },
        {
          path:'/exchangeRateList',
          name:'exchangeRateList',
          component:exchangeRateList,
        },
        {
          path:"/profitFormulaList",
          name:'profitFormulaList',
          component:profitFormulaList,
        },
        {
          path:"/profitList",
          name:"profitList",
          component:profitList,
        },
        {
          path:"/profitDetail",
          name:"profitDetail",
          component:profitDetail,
        },
        {
          path:"/getCardConsumeList",
          name:"getCardConsumeList",
          component:getCardConsumeList,
        },
        {
          path:"/DiscountTypeLogList",
          name:"DiscountTypeLogList",
          component:DiscountTypeLogList,
        },
        {
          path:"/ddGoodsRankList",
          name:"ddGoodsRankList",
          component:ddGoodsRankList,
        },
        {
          path:"/JointStatistics",
          name:"JointStatistics",
          component:JointStatistics,
        },
        {
          path:"/standbyGoodsList",
          name:"standbyGoodsList",
          component:standbyGoodsList,
        },
        {
          path:"/downloadDiscountDiffGoods",
          name:"downloadDiscountDiffGoods",
          component:downloadDiscountDiffGoods,
        },
        {
          path:"/brandDiscountList",
          name:"brandDiscountList",
          component:brandDiscountList,
        },
        {
          path:"/ShopCartList",
          name:"ShopCartList",
          component:ShopCartList,
        },
        {
          path:"/downloadShopDiffGoods",
          name:"downloadShopDiffGoods",
          component:downloadShopDiffGoods,
        },
        {
          path:"/targetList",
          name:"targetList",
          component:targetList,
        },
        {
          path:"/teamList",
          name:"teamList",
          component:teamList,
        },
        {
          path:"/offerList",
          name:"offerList",
          component:offerList,
        },
        {
          path:"/wholesaleList",
          name:"wholesaleList",
          component:wholesaleList,
        },
        {
          path:"/getWholesaleDetail",
          name:"getWholesaleDetail",
          component:getWholesaleDetail,
        },
        {
          path:"/getWholeDetail",
          name:"getWholeDetail",
          component:getWholeDetail,
        },
        {
          path:"/getOfferDetail",
          name:"getOfferDetail",
          component:getOfferDetail,
        },
        {
          path:"/userList",
          name:"userList",
          component:userList,
        },
        {
          path:"/goodsSaleList",
          name:"goodsSaleList",
          component:goodsSaleList,
        },
        {
          path:"/downloadSaleDiffGoods",
          name:"downloadSaleDiffGoods",
          component:downloadSaleDiffGoods,
        },
        {
          path:"/classifyFieldList",
          name:"classifyFieldList",
          component:classifyFieldList,
        },
        {
          path:"/getVersionList",
          name:"getVersionList",
          component:getVersionList,
        },
        {
          path:"/ltGoodsList",
          name:"ltGoodsList",
          component:ltGoodsList,
        }
      ]
    },
    {
      path:'/headr',
      name:'headr',
      component:headr
    }
    
  ]
})
// 全局路由守卫
// router.beforeEach((to,from,next)=>{  
//   // if(to.meta.requireAuth){ //是否需要登录权限 
//   //   let isLogin=sessionStorage.getItem("isTrue")
//   //   if(isLogin==="1000"){ 
//   //     next()  
//   //     // sessionStorage.removeItem("isTrue");
//   //   }else{  
//   //     next({  
//   //       path:'/',  
//   //       // query:{redirect:to.fullPath}  
//   //     }) 
//   //   } 
//   // }else{  
//   //   next();
//   // }  


// var token=sessionStorage.getItem("token");
  
    
//     if(from.name==null){
//         next();
      
//       // if(token!=null){
//       //   next()
//       // }else{
//       //   next(false);
//       // }
//   }
//   if(from.name=='Login'){
//     next();
//   }else{
//     next(false)
//   }
// }); 




export default router;



