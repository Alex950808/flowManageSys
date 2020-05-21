
# coding: utf-8

# In[1]:


#!/usr/bin/env python
# coding: utf-8

import json
import pymysql
import requests
from bs4 import BeautifulSoup
import random
import pathlib
ip_random = -1

from threading import Timer
import datetime
import time


class Mysql(object):
    # mysql 端口号,注意：必须是int类型
    def __init__(self):
        self.timeout = 50
        self.filename = '乐天_'
        self.goods_url_pre = 'http://chn.lottedfs.cn/kr/product/productDetail?prdNo='
        self.headers = {
            "user-agent":"Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36",
            'cookie':"language=zh; lang_pc=ZH; cntry_pc=KR; cntry_gate=OT; lang_gate=ZH; lang=ZH; cntry=KR; dprt.KR=D01; crc=CNY; RB_PCID=1576135074253105932; _ga=GA1.2.452049757.1576135075; RB_GUID=f338decf-fce0-45e4-bbc4-676bd82a8afa; ldfsRecentSearchWord=10002007568/a1*L8022000/a1*523M/a1*M4XA01/a1*MT1331/a1*MT1N46/a1*MLGK19/a1*M2LP06/a1*L2567100/a1*F1194400; ldfsRecentPrd=20000529528%7C22690%7C20000473961%7C20000473953%7C10002070473; inChannelCd=100343; JSESSIONID=801eddae-3b5a-4f6b-88e6-136df941e379; _gid=GA1.2.1483698451.1577683989; alliance_pc=Y; etRemoteOptions=%7B%7D; dimension={'dimension1':'PC Web','dimension2':'%EC%A4%91%EB%AC%B8_%ED%95%9C%EA%B5%AD','dimension3':"U",'dimension4':"U",'dimension5':"U",'dimension6':"U",'dimension7':"U",'dimension8':'N','dimension9':"U",'dimension10':"",'dimension11':""}; RB_SSID=coWD5Qv8oA; lodfsAdltYn=N; __z_a=2445322310426312640442631",
            'host':'chn.lottedfs.cn'
        }
        
         # 获取IP初始化变量
        self.url = 'http://www.xicidaili.com/nn/'
        #self.url = 'https://www.kuaidaili.com/free/'
        self.check_url = 'https://www.ip.cn/'

        # 获取系统配置信息
        with open('config.txt', 'r') as file:
            env_list = json.load(file)
            env_info = env_list[0]
   
        self.host = env_info['host']
        self.user = env_info['user']
        self.passwd = env_info['passwd']
        self.port = env_info['port']
        self.db_name = env_info['db_name']
        

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
        """
        获取所有数据库名
        :return: list
        """
        # 排除自带的数据库
        exclude_list = ["sys", "information_schema", "mysql", "performance_schema"]
        sql = "show databases"  # 显示所有数据库
        res = self.select(sql)
        # print(res)
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
        # print(res)
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

        # print(info_dict)
        return info_dict

    #通过乐天商品码获取商品信息
    def update_goods_info(self, need_update_goods_list):
        headers = self.headers
        timeout = self.timeout
        num = 0
        goods_url_pre = 'http://chn.lottedfs.cn/kr/product/productDetail?prdNo='
        lt_prd_no_arr = erp_ref_no_sql = erp_prd_no_sql = ''
        for prd_no in list(need_update_goods_list):
            #一次更新2条记录
            num = num + 1
            if num >2:
                break
            #组装商品详情url
            goods_detail_url = goods_url_pre+str(prd_no)
            #组装批量更新条件
            lt_prd_no_arr += str(prd_no) + "','" 
            #获取商品信息
            erp_prd_no = erp_ref_no = '11111';
            goods_detail_html = requests.get(goods_detail_url, headers=headers, timeout=timeout).content
            goods_detail_html = BeautifulSoup(goods_detail_html, 'lxml')
            if goods_detail_html.find(class_ = 'productCode1'):
                erp_ref_no = goods_detail_html.find(class_ = 'productCode1').text.split(':')[1]
                erp_prd_no = goods_detail_html.find(class_ = 'productCode').text.split(':')[1]
            elif goods_detail_html.find('input',{'name': 'erpPrdNo'}):
                erp_prd_no = goods_detail_html.find('input',{'name': 'erpPrdNo'})['value'] 
            #组装批量更新数据
            mis_goods_list[prd_no]['erp_prd_no'] = erp_prd_no    
            mis_goods_list[prd_no]['erp_ref_no'] = erp_ref_no
            erp_ref_no_sql = erp_ref_no_sql +" when '" +str(prd_no)+"' then '" +str(erp_ref_no)+"'"
            erp_prd_no_sql = erp_prd_no_sql + " when '" +str(prd_no)+"' then '" +str(erp_prd_no)+"'"
            #清理已更新商品
            del(need_update_goods_list[prd_no])
            #停止10s，以防被禁止IP
            time.sleep(10)
        #更新商品代码
        if erp_prd_no_sql:
            lt_prd_no_arr = lt_prd_no_arr[:-3]
            update_erp_prd_no_sql = "update jms_lt_goods_info set erp_prd_no = case lt_prd_no "+ erp_prd_no_sql +"end where lt_prd_no in ('"+lt_prd_no_arr+"')"
            # print(lt_prd_no_arr)
            # print(update_erp_prd_no_sql)
            self.update(update_erp_prd_no_sql)
        #更新商品参考码
        if erp_ref_no_sql:
            update_erp_ref_no_sql = "update jms_lt_goods_info set erp_ref_no = case lt_prd_no "+ erp_ref_no_sql +"end where lt_prd_no in ('"+lt_prd_no_arr+"')"
            self.update(update_erp_ref_no_sql)

        #判断需要更新商品代码的商品是否存在
        if len(need_update_goods_list) == 0:
            return

        #循环执行更新任务
        obj.update_goods_info(need_update_goods_list)

    # 执行更新任务
    def main(self):
        global mis_goods_list
        global cwd_path

        #获取mis中已经存在的商品信息
        file_path = "mis_goods_list.txt"
        path = pathlib.Path(file_path)
        if path.exists() and path.stat().st_size:
            with open(file_path, 'r') as file:
                mis_goods_list = json.load(file)

        #收集需要更新商品代码的商品
        need_update_goods_list = {}
        if mis_goods_list:
            for prd_no in mis_goods_list:
                if mis_goods_list[prd_no]['erp_prd_no'] !='':
                    continue
                need_update_goods_list[prd_no] = mis_goods_list[prd_no]
        
        # 判断需要更新商品代码的商品是否存在
        if len(need_update_goods_list) == 0:
            return

        # 通过乐天商品码获取商品信息
        obj.update_goods_info(need_update_goods_list)

        # 整理完更新后的商品数据，写入有效文件
        file_path = "mis_goods_list.txt"
        with open(file_path, 'w') as file:
            json.dump(mis_goods_list, file)

if __name__ == '__main__':

    path = pathlib.Path()
    cwd_path = path.cwd()

    obj = Mysql()
    obj.main()
    
    
   
    
    
   

    

