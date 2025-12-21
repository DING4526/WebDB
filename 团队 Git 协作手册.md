# 📘 团队 Git 协作手册（最终版）

## 一、整体协作模型（一句话版）

> **所有人只在 feature 分支写代码**
>  → **通过 pull request（PR） 合入 dev，交由负责人检查**
>  → **由负责人检查并执行 feat 向 dev 的合并**
>  → **负责人定期把 dev 合入 main 并发布**

> **一个 pull request（PR） = 一个“可以独立解释清楚、半天到一天能完成”的改动**

------

## 二、分支说明（所有人必须遵守）

| 分支        | 用途            | 谁能合并            |
| ----------- | --------------- | ------------------- |
| `main`      | 发布 / 稳定版本 | 仅负责人            |
| `dev`       | 集成分支        | PR + 负责人最终合并 |
| `feature/*` | 个人功能分支    | 分支作者            |

⚠️ **Ruleset规则禁止了任何人直接在 dev / main 上写代码**

------

## 三、当前 Rule 规则说明（非常重要）

> 我已经在 Github 仓库里定义了 Ruleset 规则来维护仓库的稳定

### 1️⃣ dev 分支规则（`dev-protection-all-PR1-owner-check`）

**规则含义：**

- ❌ 任何人不能直接 push `dev`
- ✅ 所有更新 dev **必须通过 PR**
- ✅ 每个 PR **至少 1 个 approve**
- ❗ 实际 merge **由负责人执行**
- ❌ 禁止 force push / 删除 dev

**对大家意味着什么？**

> 👉 你可以提 PR
>  👉 你不能自己 merge（需要merge时告知负责人即可）
>  👉 PR 必须有人看过（approve）
>  👉 最终由负责人合并，确保 dev 稳定

------

### 2️⃣ main 分支规则（`main-protection-owner-PR0`）

**规则含义：**

- ❌ 任何人不能直接 push `main`
- ✅ 更新 main **必须通过 PR**
- ✅ 不要求 approve（PR0）
- ❗ **只有负责人可以 merge**
- ❌ 禁止 force push / 删除 main

**对大家意味着什么？**

> 👉 你不会直接接触 main
>  👉 main 只作为发布分支存在
>  👉 不需要关心 main 的合并细节

------

## 四、 每个人的日常职责（你只需要做这些）

### 普通成员需要做的

1. 从 dev 拉 feature 分支
2. 在 feature 写代码
3. **频繁同步 dev 到自己的 feature 分支，解决冲突**
4. 提 PR → dev
5. 根据 review 修改代码

### 普通成员不需要做的

- 不碰 main
- 不处理发布

------

## 五、标准开发流程（照敲）

### Step 1：创建功能分支

```
// 从dev分支出去
git switch dev
git pull
git switch -c feature/xxx
```

------

### Step 2：日常开发 & 提交

```
// 在自己的功能分支上
git switch feature/xx
// 标准流程
git add .
git commit -m "feat: xxx"
```

随时可以 push 备份：

```
git push -u origin feature/xxx
```

---

## 六、⭐ 核心要求：频繁同步 dev（非常重要）

> **冲突一定要在 feature 分支解决，不允许留到 PR**

### 正确同步方式（推荐每天至少一次）

```
git fetch
git switch feature/xxx
git merge origin/dev
```

### 为什么要这样做？

- dev 是所有人代码的汇合点
- 别人的改动迟早会影响你
- **早合并 = 小冲突**
- **晚合并 = 大爆炸**

------

### 冲突怎么处理（统一做法）

1. merge 时出现冲突
2. 打开冲突文件，解决 `<<<< >>>>`
3. 标记解决并提交：

```
git add <冲突文件>
git commit
git push
```

如果合乱了，撤销本次 merge：

```
git merge --abort
```

------

## 七、Pull Request（PR）流程（必须遵守）

### 1️⃣ 创建 PR

- base：`dev`
- compare：`feature/xxx`
- **默认用 Draft PR**

------

### 2️⃣ PR 前自检（很重要）

- 已 merge 最新 dev
- 本地无冲突
- 功能跑过

------

### 3️⃣ Ready for review

当功能完成后，点 **Ready for review**

> #### PR 多大算“刚好”？
>
> 给你一个**简单判断标准**：
>
> | 指标          | 推荐      |
> | ------------- | --------- |
> | 改动文件数    | ≤ 10–15   |
> | 新增/修改行数 | ≤ 300–500 |
> | Review 时间   | ≤ 10 分钟 |

------

### 4️⃣ Review & 修改（如有需要）

- 有 comment 就改
- 改完直接 push
- **不要新开 PR**

------

### 5️⃣ 合并 PR（如有需要）

- 至少 1 个 approve
- **负责人执行 Squash merge**
- 合并后删除 feature 分支

------

## 八、发布流程（了解即可）

> 由负责人执行

1. PR：`dev → main`
2. 检查 & merge
3. main 打 tag 发布

------

## 九、常见 Git 操作说明（帮助理解）

### fetch vs pull

- `git fetch`：只更新远程状态，**不改你代码**
- `git pull`：fetch + merge，**可能立刻冲突**

👉 推荐：

> 先 fetch，再手动 merge

------

## 十、常见问题（队友必看）

**Q：我能不能直接 push dev？**
 ❌ 不行（规则禁止）

**Q：PR 卡住了怎么办？**
 👉 看是否：

- 没 approve
- 有未解决 comment
- 没同步最新 dev

**Q：为什么一定要我 merge dev？**
 👉 因为这是**对你自己最省时间的做法**

------

## 三条铁律（请记住）

> 1️⃣ 只在 feature 写代码
>  2️⃣ 冲突在本地解决
>  3️⃣ dev 属于团队，main 属于发布