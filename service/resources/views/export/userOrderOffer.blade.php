<link rel= 'stylesheet' href = 'css/export.css' />

<table>
    <tbody>
    <tr>
        <td class = 'userOrderOfferTitle'>商品名称</td>
        <td class = 'userOrderOfferTitle'>规格码</td>
        <td class = 'userOrderOfferTitle'>商家编码</td>
        <td class = 'userOrderOfferTitle'>平台条码</td>
        <td class = 'userOrderOfferTitle'>美金原价</td>
        <td class = 'userOrderOfferTitle'>EXW折扣</td>
        <td class = 'userOrderOfferTitle'>销售折扣</td>
    </tr>
    @foreach( $data as $k => $item)
        <tr>
            <td>{{ $item->goods_name }}</td>
            <td>{{ $item->spec_sn }}</td>
            <td>{{ $item->erp_merchant_no }}</td>
            <td>{{ $item->platform_barcode }}</td>
            <td>{{ $item->spec_price }}</td>
            <td>{{ $item->exw_discount }}</td>
            <td>{{ $item->sale_discount }}</td>
        </tr>
    @endforeach
    </tbody>
</table>