# -*- coding: utf-8 -*-
import requests
import json
import csv
import xlwt
import random
import re
from datetime import datetime
import time
from bs4 import BeautifulSoup
import re
import openpyxl
import pymysql
import pathlib
from threading import Timer
from re import sub

ip_random = -1

mis_goods_list = {}

cwd_path = ''

class LT_producs(object):
    def __init__(self):
        self.is_protected = 0
        self.timeout = 40
        self.filename = '乐天_'
        self.url = 'http://chn.lottedfs.cn/kr'
        self.goods_url = 'http://chn.lottedfs.cn/kr/display/GetPrdList?viewType01=0&lodfsAdltYn=N&sortStdCd=01&catNo={}&dispShopNo={}&cntPerPage=240&curPageNo={}'
        self.cat_url = 'http://chn.lottedfs.cn'
        self.headers = {
            "user-agent":"Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36",
            'cookie':"language=zh; lang_pc=ZH; cntry_pc=KR; cntry_gate=OT; lang_gate=ZH; lang=ZH; cntry=KR; dprt.KR=D01; crc=CNY; RB_PCID=1576135074253105932; _ga=GA1.2.452049757.1576135075; RB_GUID=f338decf-fce0-45e4-bbc4-676bd82a8afa; ldfsRecentSearchWord=10002007568/a1*L8022000/a1*523M/a1*M4XA01/a1*MT1331/a1*MT1N46/a1*MLGK19/a1*M2LP06/a1*L2567100/a1*F1194400; ldfsRecentPrd=20000529528%7C22690%7C20000473961%7C20000473953%7C10002070473; inChannelCd=100343; JSESSIONID=801eddae-3b5a-4f6b-88e6-136df941e379; _gid=GA1.2.1483698451.1577683989; alliance_pc=Y; etRemoteOptions=%7B%7D; dimension={'dimension1':'PC Web','dimension2':'%EC%A4%91%EB%AC%B8_%ED%95%9C%EA%B5%AD','dimension3':"U",'dimension4':"U",'dimension5':"U",'dimension6':"U",'dimension7':"U",'dimension8':'N','dimension9':"U",'dimension10':"",'dimension11':""}; RB_SSID=coWD5Qv8oA; lodfsAdltYn=N; __z_a=2445322310426312640442631",
            'host':'chn.lottedfs.cn'
        }

        # 获取系统配置信息
        with open('config.txt', 'r') as file:
            config_list = json.load(file)
            config_info = config_list[0]
   
        self.host = config_info['host']
        self.user = config_info['user']
        self.passwd = config_info['passwd']
        self.port = config_info['port']
        self.db_name = config_info['db_name']

    def select(self, sql):
        """
        执行sql命令
        :param sql: sql语句
        :return: 元祖
        """
        try:
            conn = pymysql.connect(
                host=self.host,
                user=self.user,
                passwd=self.passwd,
                port=self.port,
                database=self.db_name,
                charset='utf8',
                cursorclass=pymysql.cursors.DictCursor
            )
            cur = conn.cursor()  # 创建游标
            # conn.cursor()
            cur.execute(sql)  # 执行sql命令
            res = cur.fetchall()  # 获取执行的返回结果
            cur.close()
            conn.close()
            return res
        except Exception as e:
            print(e)
            return False
    def insert(self, sql):
        """
        执行sql命令
        :param sql: sql语句
        :return: 元祖
        """
        try:
            conn = pymysql.connect(
                host=self.host,
                user=self.user,
                passwd=self.passwd,
                port=self.port,
                database=self.db_name,
                charset='utf8',
                cursorclass=pymysql.cursors.DictCursor
            )
            cur = conn.cursor()  # 创建游标
            res = cur.execute(sql)  # 执行sql命令
            #res = cur.executemany(sql,data)
            conn.commit()#执行update操作时需要写这个，否则就会更新不成功
            cur.close()
            conn.close()
            return res
        except Exception as e:
            print(e)
            return False
    def update(self, sql):
        """
        执行sql命令
        :param sql: sql语句
        :return: 元祖
        """
        try:
            conn = pymysql.connect(
                host=self.host,
                user=self.user,
                passwd=self.passwd,
                port=self.port,
                database=self.db_name,
                charset='utf8',
                cursorclass=pymysql.cursors.DictCursor
            )
            cur = conn.cursor()  # 创建游标
            res = cur.execute(sql)  # 执行sql命令
            conn.commit()#执行update操作时需要写这个，否则就会更新不成功
            cur.close()
            conn.close()
            return res
        except Exception as e:
            print(e)
            return False

    def get_all_db(self):
        # 获取所有数据库名
        # 排除自带的数据库
        exclude_list = ["sys", "information_schema", "mysql", "performance_schema"]
        sql = "show databases"  # 显示所有数据库
        res = self.select(sql)
        if not res:  # 判断结果非空
            return False

        db_list = []  # 数据库列表
        for i in res:
            db_name = i['Database']
            # 判断不在排除列表时
            if db_name not in exclude_list:
                db_list.append(db_name)
                # print(db_name)

        if not db_list:
            return False
        return db_list

    def get_user_list(self):
        """
        获取用户列表
        :return: list
        """
        # 排除自带的用户
        exclude_list = ["root", "mysql.sys", "mysql.session"]
        sql = "select User from mysql.user"
        res = self.select(sql)

        if not res:  # 判断结果非空
            return False

        user_list = []
        for i in res:
            db_name = i['User']
            # 判断不在排除列表时
            if db_name not in exclude_list:
                user_list.append(db_name)

        if not user_list:
            return False
        return user_list

    def get_user_power(self):
        """
        获取用户权限
        :return: {}

        {
            "test":{  # 用户名
                "read":["db1","db2"],  # 只拥有读取权限的数据库
                "all":["db1","db2"],  # 拥有读写权限的数据库
            },
            ...
        }
        """
        info_dict = {}  # 最终结果字典
        # 获取用户列表
        user_list = self.get_user_list()
        if not user_list:
            return False

        # 查询每一个用户的权限
        for user in user_list:
            # print("user",user)
            sql = "show grants for {}".format(user)
            res = self.select(sql)
            if not res:
                return False

            for i in res:
                key = 'Grants for {}@%'.format(user)
                # print("key",key)
                # 判断key值存在时
                if i.get(key):
                    # print(i[key])
                    # 包含ALL或者SELECT时
                    if "ALL" in i[key] or "SELECT" in i[key]:
                        # print(i[key])
                        if not info_dict.get(user):
                            info_dict[user] = {"read": [], "all": []}

                        cut_str = i[key].split()  # 空格切割
                        # print(cut_str,len(cut_str))
                        power = cut_str[1]  # 权限，比如ALL，SELECT

                        if len(cut_str) == 6:  # 判断切割长度
                            # 去除左边的`
                            tmp_str = cut_str[3].lstrip("`")
                        else:
                            tmp_str = cut_str[4].lstrip("`")

                        # 替换字符串
                        tmp_str = tmp_str.replace('`.*', '')
                        value = tmp_str.replace('\_', '-')

                        # 判断权限为select 时
                        if power.lower() == "select":
                            if value not in info_dict[user].get("read"):
                                # 只读列表
                                info_dict[user]["read"].append(value)
                        else:
                            if value not in info_dict[user].get("all"):
                                # 所有权限列表
                                info_dict[user]["all"].append(value)
        return info_dict

    # 发起请求
    def get_html(self,url):
        global header
        header = {
            "user-agent":"Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36",
            'cookie':"language=zh; lang_pc=ZH; cntry_pc=KR; cntry_gate=OT; lang_gate=ZH; lang=ZH; cntry=KR; dprt.KR=D01; crc=CNY; RB_PCID=1576135074253105932; _ga=GA1.2.452049757.1576135075; RB_GUID=f338decf-fce0-45e4-bbc4-676bd82a8afa; ldfsRecentSearchWord=10002007568/a1*L8022000/a1*523M/a1*M4XA01/a1*MT1331/a1*MT1N46/a1*MLGK19/a1*M2LP06/a1*L2567100/a1*F1194400; ldfsRecentPrd=20000529528%7C22690%7C20000473961%7C20000473953%7C10002070473; inChannelCd=100343; JSESSIONID=801eddae-3b5a-4f6b-88e6-136df941e379; _gid=GA1.2.1483698451.1577683989; alliance_pc=Y; etRemoteOptions=%7B%7D; dimension={'dimension1':'PC Web','dimension2':'%EC%A4%91%EB%AC%B8_%ED%95%9C%EA%B5%AD','dimension3':"U",'dimension4':"U",'dimension5':"U",'dimension6':"U",'dimension7':"U",'dimension8':'N','dimension9':"U",'dimension10':"",'dimension11':""}; RB_SSID=coWD5Qv8oA; lodfsAdltYn=N; __z_a=2445322310426312640442631",
            'host':'chn.lottedfs.cn'
        }

        global ip_random
        ip_rand, proxies = self.get_proxie(ip_random)
        try:
            request = requests.get(url=url, headers=header, proxies=proxies, timeout=20)
        except:
            request_status = 500
        else:
            request_status = request.status_code
        while request_status != 200:
            ip_random = -1
            ip_rand, proxies = self.get_proxie(ip_random)
            try:
                request = requests.get(url=url, headers=header, proxies=proxies, timeout=20)
            except:
                request_status = 500
            else:
                request_status = request.status_code
        ip_random = ip_rand
        request.encoding = 'gbk'
        html = request.content
        return html

    # 获取有效ip
    def get_proxie(self,random_number):
        with open('ip.txt', 'r') as file:
            ip_list = json.load(file)
            if random_number == -1:
                random_number = random.randint(0, len(ip_list) - 1)
            ip_info = ip_list[random_number]
            ip_url_next = '://' + ip_info['address'] + ':' + ip_info['port']
            proxies = {'http': 'http' + ip_url_next, 'https': 'https' + ip_url_next}
            return random_number, proxies

    def get_total_cat(self):
        '''提取所有分类信息'''
        total_cat_html = requests.get(self.url, headers=self.headers, timeout=self.timeout).content
        total_cat = BeautifulSoup(total_cat_html, 'lxml')
        total_cat_info = total_cat.find_all(class_='cateMenu')
        
        if len(total_cat_info)< 1:
            self.is_protected = 1
            total_cat_html = self.get_html(self.url)
            total_cat = BeautifulSoup(total_cat_html, 'lxml')
            total_cat_info = total_cat.find_all(class_='cateMenu')
        return total_cat_info
    def str_format(self,string):
        string = re.sub('[\r\n\t]', '', string)
        return string

    def get_products(self,cat_url,goods_type):
        '''提取商品信息'''

        dispShopNo1 = cat_url.split('=')[-2].split('&')[0]
        headers = {
            "user-agent":"Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36",
            'cookie':"language=zh; lang_pc=ZH; cntry_pc=KR; cntry_gate=OT; lang_gate=ZH; lang=ZH; cntry=KR; dprt.KR=D01; crc=CNY; RB_PCID=1576135074253105932; _ga=GA1.2.452049757.1576135075; RB_GUID=f338decf-fce0-45e4-bbc4-676bd82a8afa; ldfsRecentSearchWord=10002007568/a1*L8022000/a1*523M/a1*M4XA01/a1*MT1331/a1*MT1N46/a1*MLGK19/a1*M2LP06/a1*L2567100/a1*F1194400; ldfsRecentPrd=20000529528%7C22690%7C20000473961%7C20000473953%7C10002070473; inChannelCd=100343; JSESSIONID=801eddae-3b5a-4f6b-88e6-136df941e379; _gid=GA1.2.1483698451.1577683989; alliance_pc=Y; etRemoteOptions=%7B%7D; dimension={'dimension1':'PC Web','dimension2':'%EC%A4%91%EB%AC%B8_%ED%95%9C%EA%B5%AD','dimension3':"U",'dimension4':"U",'dimension5':"U",'dimension6':"U",'dimension7':"U",'dimension8':'N','dimension9':"U",'dimension10':"",'dimension11':""}; __z_a=2445322310426312640442631; __ZEHIC9062=1577698858; RB_SSID=9FKErUJFs8; __zjc3379=4956487110; _dc_gtm_UA-84350687-2=1; _dc_gtm_UA-84350687-3=1; lodfsAdltYn=null",
            'host':'chn.lottedfs.cn'
        }
       #获取最大页码
        goods_url = self.goods_url.format(dispShopNo1, dispShopNo1, 1)
        total_goods_html = requests.get(goods_url, headers=headers, timeout=self.timeout).content
        total_goods_info = BeautifulSoup(total_goods_html, 'lxml')
        if total_goods_info.find(class_='last'):
            page_info = total_goods_info.find(class_='last')['href']
        else:
            total_goods_html = self.get_html(goods_url)
            total_goods_info = BeautifulSoup(total_goods_html, 'lxml')
            if total_goods_info.find(class_='last'):
                page_info = total_goods_info.find(class_='last')['href']
            else:
                self.get_products(cat_url)
        last_page = re.findall(r'[(](.*?)[)]', page_info)[0]

        #获取每页商品数据
        for i in range(1,int(last_page)+1):
        #for i in range(1,2):
            goods_url = self.goods_url.format(dispShopNo1, dispShopNo1, i)
            file_pre_path = cwd_path/'log/request'
            self.write_file(file_pre_path, '请求开始', goods_url)

            total_goods_html = requests.get(goods_url, headers=headers, timeout=self.timeout).content
            total_goods_info = BeautifulSoup(total_goods_html, 'lxml')
            if total_goods_info.find_all(class_='productMd'):
                goods_info = total_goods_info.find_all(class_='productMd')
            else:
                total_goods_html = self.get_html(goods_url)
                total_goods_info = BeautifulSoup(total_goods_html, 'lxml')
                if total_goods_info.find_all(class_='productMd'):
                    goods_info = total_goods_info.find_all(class_='productMd')
                else:
                    i = i -1
                    continue
            if len(goods_info) < 1 :
                self.write_file(file_pre_path, '请求结束', '请求失败')
                continue;
            self.write_file(file_pre_path, '请求结束', '请求成功')

            goods_list = []
            update_spec_price_arr = []
            for index in range(len(goods_info)):
                if goods_info[index].find(class_='btn3 dgray toastBtn relProduct') is None:
                    continue
                    
                #获取产品码
                if goods_info[index].find(class_='btn3 dgray toastBtn relProduct'):
                    prdNo = goods_info[index].find(class_ = 'btn3 dgray toastBtn relProduct')['data-prd-no']
                #获取品牌信息
                brand_cn_name = brand_en_name = ''
                if goods_info[index].find(class_ = 'brand'):
                    brand_cn_name = self.str_format(goods_info[index].find(class_ = 'brand').find('strong').text)
                    brand_en_name = self.str_format(goods_info[index].find(class_ = 'brand').text)
                #获取商品名称
                goods_name = ''
                if goods_info[index].find(class_ = 'product'):
                    goods_name = goods_info[index].find(class_ = 'product').text
                #获取美金原价信息
                spec_price = spec_discount_price = cny_price = 0
                if goods_info[index].find(class_ = 'discount'):
                    spec_discount_price = goods_info[index].find(class_ = 'discount').find('strong').text[1:] 
                    spec_price = spec_discount_price = float(sub(r'[^\d.]', '', str(spec_discount_price)))

                    cny_price = goods_info[index].find(class_ = 'discount').find('span').text
                    cny_price = cny_price.replace('约', '')
                    cny_price = cny_price.replace('元', '')
                    cny_price = float(sub(r'[^\d.]', '', str(cny_price)))
                #获取美金折扣价信息
                if goods_info[index].find(class_ = 'cancel'):
                    spec_price = goods_info[index].find(class_ = 'cancel').text[1:] 
                    spec_price = float(sub(r'[^\d.]', '', str(spec_price)))
                
                now_time=datetime.now()
                now_day = now_time.strftime("%Y-%m-%d")

                prdNo = str(prdNo)
                erp_ref_no = erp_prd_no = '';
                if mis_goods_list.get(prdNo):
                    old_goods_info = mis_goods_list[prdNo]
                    erp_ref_no = str(old_goods_info['erp_ref_no'])
                    erp_prd_no = str(old_goods_info['erp_prd_no'])
                    old_spec_price = float(old_goods_info['spec_price'])
                    old_spec_discount_price = float(old_goods_info['spec_discount_price'])
                    old_cny_price = float(old_goods_info['cny_price'])

                    if cny_price != old_cny_price:
                        mis_goods_list[prdNo]['cny_price'] = cny_price

                    #更新mis商品表中的美金原价
                    if erp_prd_no:
                        if spec_price:
                            mis_goods_list[prdNo]['spec_price'] = spec_price
                            update_spec_price_arr.append((erp_prd_no, spec_price))
                        elif spec_price == 0:
                            update_spec_price_arr.append((erp_prd_no, spec_discount_price))

                        if spec_discount_price and spec_discount_price != old_spec_discount_price:
                            mis_goods_list[prdNo]['spec_discount_price'] = spec_discount_price

                goods_list.append((now_day, brand_en_name,brand_cn_name,goods_name,prdNo,spec_price,spec_discount_price,cny_price,goods_type,erp_ref_no,erp_prd_no))

            # 新增商品
            page_goods_num = len(goods_list)
            if goods_list:
                insert_sql = "INSERT INTO jms_lt_goods_info(download_date, brand_name, brand_cn_name, goods_name, lt_prd_no, spec_price, spec_discount_price, cny_price, goods_type, erp_ref_no, erp_prd_no) VALUES "
                add_str = ''
                for i in range(len(goods_list)):
                    add_str += str(goods_list[i])
                    add_str += ","
                if add_str:
                    insert_sql += add_str
                    insert_sql = insert_sql[:-1]
                    self.insert(insert_sql)

                    file_pre_path = cwd_path/'log/insert'
                    title = '新增商品:共'+str(page_goods_num)+'条'
                    self.write_file(file_pre_path, title, insert_sql)

            # 更新美金原价
            if update_spec_price_arr:
                update_spec_price_str = spec_sn = ''
                for i,row in enumerate(update_spec_price_arr):
                    update_spec_price_str += " when '" +str(row[0])+"' then '" + str(row[1])+"'"
                    spec_sn += str(row[0])
                    spec_sn += "','"

                update_spec_price_sql = "update jms_goods_spec set spec_price = case erp_prd_no"
                spec_sn = spec_sn[:-3]
                update_spec_price_sql += update_spec_price_str
                update_spec_price_sql += " end where erp_prd_no in ('"+spec_sn+"')"
                self.update(update_spec_price_sql)
                #记录日志
                file_pre_path = cwd_path/'log/update'
                self.write_file(file_pre_path, '更新美金原价', update_spec_price_sql)

    def write_file(self, file_pre_path, title, content):
        now_time=datetime.now()
        now_day = now_time.strftime("%Y-%m-%d")
        now_time = now_time.strftime("%H-%M-%S") + " : "
        update_file_name = now_day + '.txt'
        final_update_file_path = file_pre_path/update_file_name
        with open(final_update_file_path, 'a', encoding='utf-8') as file:
            file.write(str('['+ title +']' + now_time)+"\n")
            file.write(str(content)+"\n")
            file.close()

    # 获取mis已经存在的商品
    def get_mis_goods(self):
        global mis_goods_list
        global cwd_path

        file_path = cwd_path/"mis_goods_list.txt"
        path = pathlib.Path(file_path)
        if path.exists() and path.stat().st_size:
            with open(file_path, 'r') as file:
                mis_goods_list = json.load(file)

        lt_prd_no_arr = ''
        if mis_goods_list:
            for lt_prd_no in mis_goods_list:
                #每日更新一次缓存文件中的erp_prd_no
                if mis_goods_list[lt_prd_no]['erp_prd_no'] ==11111:
                    mis_goods_list[lt_prd_no]['erp_prd_no'] =''
                #组装缓存文件中已经存在的乐天商品码
                if lt_prd_no_arr.find(lt_prd_no) == -1:
                    lt_prd_no_arr += "'" + str(lt_prd_no) +"',"
                    
        mis_new_goods = []
        su_sql = "select * from jms_lt_goods_info where id in (select max(id) from jms_lt_goods_info group by lt_prd_no)"
        if lt_prd_no_arr:
            lt_prd_no_arr = lt_prd_no_arr[:-1];
            su_sql = "select * from jms_lt_goods_info where id in (select max(id) from jms_lt_goods_info group by lt_prd_no) and lt_prd_no not in (" +lt_prd_no_arr+ ")"
        mis_new_goods = self.select(su_sql)

        if mis_new_goods:
            for info in mis_new_goods:
                lt_prd_no = str(info['lt_prd_no'])
                erp_ref_no = str(info['erp_ref_no'])
                erp_prd_no = str(info['erp_prd_no'])

                spec_price = str(info['spec_price'])
                spec_discount_price = str(info['spec_discount_price'])
                cny_price = str(info['cny_price'])

                arr = {"lt_prd_no":lt_prd_no, "erp_ref_no":erp_ref_no, 
                    "erp_prd_no":erp_prd_no, "spec_price":spec_price, 
                    "spec_discount_price":spec_discount_price, "cny_price":cny_price}
                
                lt_prd_no_index = str(info['lt_prd_no'])
                mis_goods_list[lt_prd_no_index] = arr

        # 写入有效文件
        with open(file_path, 'w') as file:
            json.dump(mis_goods_list, file)


    def main(self):
        global cwd_path
        # 整理已经存在的商品
        self.get_mis_goods()
        time.sleep(20) 
        
        # 获取商品分类
        total_cat = self.get_total_cat()
        workbook=xlwt.Workbook(encoding='utf-8')
        booksheet=workbook.add_sheet('letian', cell_overwrite_ok=True)
        for index in range(len(total_cat)):
            if index < 3:
            #if index == 2:
                cat_url = self.cat_url + total_cat[index]['href']
                print(cat_url)
                goods_type = index + 1
                self.get_products(cat_url, goods_type) 

        # 写入有效文件
        file_path = cwd_path/"mis_goods_list.txt"
        with open(file_path, 'w') as file:
            json.dump(mis_goods_list, file)
if __name__ == '__main__':

    # 创建日志目录
    path = pathlib.Path()
    cwd_path = path.cwd()
    log_path_arr = ['insert','update','request'];
    for path in log_path_arr:
        tmp_path = cwd_path/'log'/path
        if tmp_path.exists() is False:
            tmp_path.mkdir(parents=True)
    
    # 主方法，进行新品新增和商品美金原价更新
    tm = LT_producs()
    tm.main()

    
    
    


   
    
    
   

    

