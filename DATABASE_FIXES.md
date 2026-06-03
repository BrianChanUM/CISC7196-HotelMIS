# 数据库修复记录

## 修复日期：2026-05-30

### 修复内容：

1. **userhistory 表 - UNIQUE 约束问题**
   - 问题：UserID 字段设置了 UNIQUE 约束，每个用户只能有一条历史记录
   - 修复：移除了 UserID 字段的 UNIQUE 约束，保留外键约束
   - 影响：用户现在可以有多条历史记录

2. **usertimesheet 表 - UNIQUE 约束问题**
   - 问题：UID 字段设置了 UNIQUE 约束，每个用户只能有一条工时记录
   - 修复：移除了 UID 字段的 UNIQUE 约束
   - 影响：用户现在可以有多条工时记录

3. **accessrole 表 - 重复数据问题**
   - 问题：RoleID 1/2/3 各重复了 3 次
   - 修复：删除了重复数据，保留每个 RoleID 一条记录
   - 影响：查询结果不再包含重复数据

### SQL 修复命令：
```sql
-- 修复 userhistory 表（外键已自动转换为普通索引）
-- 修复 usertimesheet 表
ALTER TABLE usertimesheet DROP INDEX UID;

-- 修复 accessrole 表
DELETE FROM accessrole WHERE RoleID=1 LIMIT 2;
DELETE FROM accessrole WHERE RoleID=2 LIMIT 2;
DELETE FROM accessrole WHERE RoleID=3 LIMIT 2;
```

### 修复后表状态：
- userhistory: UserID 字段不再是 UNIQUE，允许每个用户多条记录
- usertimesheet: UID 字段不再是 UNIQUE，允许每个用户多条工时记录
- accessrole: 每个 RoleID 只有一条记录（Admin, Staff, Guest）