<style>
    table,table tr th, table tr td {
        border:1px solid #000000;
        padding: 5px 10px;
    }
    table {
        min-height: 25px;
        line-height: 25px;
        text-align: center;
        border-collapse: collapse;
    }
    .title {
        font-weight: bold;
        font-size: 11px;
        background-color: #FFFFFF;
        height: 45px;
        text-align: center;
        vertical-align: middle;
        wrap-text: true;
    }
    /*水平居中*/
    .textAlign {
        text-align: center;
    }
    /*A类产品有搭配比例 颜色为 #EEC1B2*/
    .A_rateColor {
        background-color:#EEC1B2;
    }
    /*A类产品无搭配比例颜色为 #FCD5B4*/
    .A_color {
        background-color:#FCD5B4;
    }
    /*C类产品颜色为#B5CFCB*/
    .C_color {
        background-color:#B5CFCB;
    }
</style>
<table>
    <tr class="title">
        <th width="7px">分类</th>
        <th width="7px">搭配比例</th>
        <th>品牌</th>
        <th width="60px">名称</th>
        <th>商品代码</th>
        <th>货号</th>
        <th width="7px">美金原价</th>
        <th width="7px">个数</th>
        <th width="7px">美金总价</th>
        <th width="10px" style="background-color: #FFFF00">4档基础折扣率</th>
        <th width="10px" style="background-color: #FFFF00">追加后折扣率</th>
        <th width="10px">港币</th>
        <th width="10px">裸重(KG)</th>
        <th width="10px">总重量(KG)</th>
        <th width="10px">人民币汇率</th>
        <th>单价</th>
    </tr>
    {{--A类产品有搭配比例 颜色为 #EEC1B2--}}
    @foreach( $data['cat_A_rate'] as $k => $item)
        <tr>
            <td class="A_rateColor textAlign">{{ $item->cat_name }}</td>
            <td class="A_rateColor textAlign">{{ $item->match_scale }}</td>
            <td class="A_rateColor">{{ $item->brand_name }}</td>
            <td class="A_rateColor">{{ $item->goods_name }}</td>
            <td class="A_rateColor textAlign">{{ $item->erp_prd_no }}</td>
            <td class="A_rateColor textAlign">{{ $item->erp_ref_no }}</td>
            <td class="A_rateColor">{{ $item->spec_price }}</td>
            <td class="A_rateColor"></td>
            <td class="A_rateColor"></td>
            <td class="A_rateColor textAlign">{{ $item->standard_discount }}</td>
            <td class="A_rateColor textAlign">{{ $item->lastDiscount }}</td>
            <td class="A_rateColor"></td>
            <td class="A_rateColor"></td>
            <td class="A_rateColor"></td>
            <td class="A_rateColor textAlign">{{ $item->rmbPaidRate }}</td>
            <td class="A_rateColor"></td>
        </tr>
    @endforeach
    {{--A类产品无搭配比例颜色为 #FCD5B4--}}
    @foreach( $data['cat_A'] as $k => $item)
        <tr>
            <td class="A_color textAlign">{{ $item->cat_name }}</td>
            <td class="A_color textAlign">{{ $item->match_scale }}</td>
            <td class="A_color">{{ $item->brand_name }}</td>
            <td class="A_color">{{ $item->goods_name }}</td>
            <td class="A_color textAlign">{{ $item->erp_prd_no }}</td>
            <td class="A_color textAlign">{{ $item->erp_ref_no }}</td>
            <td class="A_color">{{ $item->spec_price }}</td>
            <td class="A_color"></td>
            <td class="A_color"></td>
            <td class="A_color textAlign">{{ $item->standard_discount }}</td>
            <td class="A_color textAlign">{{ $item->lastDiscount }}</td>
            <td class="A_color"></td>
            <td class="A_color"></td>
            <td class="A_color"></td>
            <td class="A_color textAlign">{{ $item->rmbPaidRate }}</td>
            <td class="A_color"></td>
        </tr>
    @endforeach
    {{--C类产品颜色为#B5CFCB--}}
    @foreach( $data['cat_C'] as $k => $item)
        <tr>
            <td class="C_color textAlign">{{ $item->cat_name }}</td>
            <td class="C_color textAlign">{{ $item->match_scale }}</td>
            <td class="C_color">{{ $item->brand_name }}</td>
            <td class="C_color">{{ $item->goods_name }}</td>
            <td class="C_color textAlign">{{ $item->erp_prd_no }}</td>
            <td class="C_color textAlign">{{ $item->erp_ref_no }}</td>
            <td class="C_color">{{ $item->spec_price }}</td>
            <td class="C_color"></td>
            <td class="C_color"></td>
            <td class="C_color textAlign">{{ $item->standard_discount }}</td>
            <td class="C_color textAlign">{{ $item->lastDiscount }}</td>
            <td class="C_color"></td>
            <td class="C_color"></td>
            <td class="C_color"></td>
            <td class="C_color textAlign">{{ $item->rmbPaidRate }}</td>
            <td class="C_color"></td>
        </tr>
    @endforeach
</table>