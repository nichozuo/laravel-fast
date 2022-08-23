# php artisan command list

## db:cache
- 每次更新完数据库以后，需要运行这个
- 这个会把当前所有表的结构缓存起来，以便后续生成模型等文件
- 这个可以写到DatabaseSeeder.php里去
```bash
php artisan db:cache
```
## dt
- 根据表名，dump表的结构
- 会自动转成复数形式
```bash
php artisan dt admin
```

## gf 生成模型
- 表名：会自动转换：驼峰，复数
- -d 生成模型
- -f 参数表示覆盖：BaseModel会被覆盖，Model不会被覆盖
```bash
php artiasn gf admin -d -f
# 已生成文件::/Users/zuowenbo/Sources/.../app/Models/Base/BaseAdmins.php
# 已生成文件::/Users/zuowenbo/Sources/.../app/Models/Admins.php
```
- 运行命令后，会生成两个文件
  - 一个BaseModel.php：自动生成的都在这里，可以被-f参数覆盖的
  - 一个Model.php：自己后续加的方法等都写在这里，不会被-f参数覆盖。业务上也用这个，会继承于BaseModel

## gf 生成控制器
- 文件夹/表名：会自动转换：驼峰，复数
- -c 生成控制器
- -f 参数表示覆盖
```bash
php artisan gf admin/admin -c -f
# 已生成文件::/Users/zuowenbo/Sources/.../app/Modules/Admin/AdminsController.php
```
- 运行命令后，会生成控制器文件

## gf 生成测试文件
- 文件夹/表名：会自动转换：驼峰，复数
- -t 生成测试文档
- -f 参数表示覆盖
```bash
php artisan gf admin/admin -t -f
# 已生成文件::/Users/zuowenbo/Sources/.../app/../tests/Modules/Admin/AdminsControllerTest.php
```
- 运行命令后，会生成测试文件
  - 自动根据controller文件中的方法，自动生成testMethod
  - 自动根据controller中的$params参数设置，自动生成请求参数

## 批量重新生成模型文件
- 会遍历所有的数据库表
- 并执行 php artisan gf 表明 -d -f
- BaseModel会被覆盖，Model不会被覆盖
```bash
php artisan update:models
```

## 备份数据到iseed中
- 把common.php里定义的iSeedBackupList的表的数据批量备份
```bash
php artisan backup:iseed
```