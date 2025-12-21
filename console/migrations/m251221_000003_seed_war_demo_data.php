<?php

use yii\db\Migration;

/**
 * Ding 2310724
 * 初始化演示数据（stage/event/person/关系/标签/留言）
 *
 * 说明：
 * - 使用固定 ID，避免不同数据库/环境下 lastInsertId 不一致导致关联错乱
 * - 防重：若 war_event 已有数据，则认为已初始化过，直接跳过
 * - 回滚：按固定 ID 删除本脚本插入的数据
 */
class m251221_000003_seed_war_demo_data extends Migration
{
    public function up()
    {
        // 必要表存在性检查
        $needTables = [
            '{{%war_stage}}',
            '{{%war_event}}',
            '{{%war_person}}',
            '{{%war_event_person}}',
            '{{%tag}}',
            '{{%war_event_tag}}',
            '{{%war_person_tag}}',
            '{{%war_message}}',
        ];
        foreach ($needTables as $t) {
            if ($this->db->schema->getTableSchema($t, true) === null) {
                // 如果缺表，直接返回 true（避免中断 migrate）
                return true;
            }
        }

        // 防重：以 war_event 为“是否已初始化”的判断点
        $already = (int)(new \yii\db\Query())->from('{{%war_event}}')->count();
        if ($already > 0) {
            return true;
        }

        $now = time();

        // -------------------------
        // 1) war_stage
        // -------------------------
        $this->batchInsert('{{%war_stage}}',
            ['id','name','start_year','end_year','description','sort_order','status','created_at','updated_at'],
            [
                [1,'局部抗战',1931,1936,'九一八事变后至全面抗战爆发前的抗日斗争阶段',10,1,$now,$now],
                [2,'全面抗战',1937,1945,'七七事变后全国范围全面抗战阶段',20,1,$now,$now],
            ]
        );

        // -------------------------
        // 2) war_person
        // -------------------------
        $this->batchInsert('{{%war_person}}',
            ['id','name','role_type','birth_year','death_year','intro','biography','status','created_at','updated_at'],
            [
                [1,'张自忠','将领',1891,1940,'抗战殉国将领','在抗战中壮烈殉国。',1,$now,$now],
                [2,'佟麟阁','将领',1892,1937,'抗战殉国将领','七七事变后参与作战。',1,$now,$now],
                [3,'赵登禹','将领',1898,1937,'抗战殉国将领','卢沟桥事变后作战牺牲。',1,$now,$now],
                [4,'左权','将领',1905,1942,'八路军高级将领','在反“扫荡”作战中牺牲。',1,$now,$now],
                [5,'白求恩','国际友人',1890,1939,'国际主义战士','在根据地救治伤员并牺牲。',1,$now,$now],
                [6,'范长江','记者',1909,1970,'战地记者','以报道记录战时中国。',1,$now,$now],
                [7,'宋庆龄','社会人士',1893,1981,'抗战时期重要社会活动家','组织救援与对外宣传。',1,$now,$now],
                [8,'普通民众（代表）','群众',null,null,'民众抗战代表形象','用于展示民众力量（示例人物）。',1,$now,$now],
            ]
        );

        // -------------------------
        // 3) war_event
        // -------------------------
        $this->batchInsert('{{%war_event}}',
            ['id','title','event_date','stage_id','summary','content','location','status','created_at','updated_at'],
            [
                [1,'九一八事变','1931-09-18',1,'日本发动侵略东北的重大事件','九一八事变标志着日本对中国的侵略进一步升级。','沈阳',1,$now,$now],
                [2,'一二八淞沪抗战','1932-01-28',1,'上海抗战爆发','淞沪地区发生激烈战斗，民众抗日情绪高涨。','上海',1,$now,$now],
                [3,'西安事变','1936-12-12',1,'促成第二次国共合作的重要事件','西安事变推动全国抗日民族统一战线形成。','西安',1,$now,$now],
                [4,'卢沟桥事变（七七事变）','1937-07-07',2,'全面抗战爆发','七七事变后全国进入全面抗战阶段。','北京宛平',1,$now,$now],
                [5,'平津作战','1937-07-11',2,'平津地区作战','平津地区发生多次战斗，出现多位殉国将领。','北平/天津',1,$now,$now],
                [6,'淞沪会战','1937-08-13',2,'上海会战','大规模会战，持续时间长，影响深远。','上海',1,$now,$now],
                [7,'南京保卫战与南京大屠杀','1937-12-13',2,'南京沦陷与惨案发生','南京陷落后发生惨绝人寰的暴行。','南京',1,$now,$now],
                [8,'百团大战','1940-08-20',2,'敌后战场大规模进攻作战','百团大战打击日军交通线，振奋士气。','华北',1,$now,$now],
                [9,'太行反“扫荡”作战','1942-05-01',2,'反“扫荡”作战','敌后根据地反“扫荡”作战艰苦激烈。','太行山区',1,$now,$now],
                [10,'日本宣布无条件投降','1945-08-15',2,'抗战胜利的重要节点','日本宣布无条件投降，抗战胜利。','全国',1,$now,$now],
            ]
        );

        // -------------------------
        // 4) war_event_person（多对多）
        // -------------------------
        $this->batchInsert('{{%war_event_person}}',
            ['id','event_id','person_id','relation_type','created_at','updated_at'],
            [
                [1,4,2,'参战', $now,$now],
                [2,4,3,'参战', $now,$now],
                [3,5,2,'殉国', $now,$now],
                [4,5,3,'殉国', $now,$now],
                [5,6,8,'见证', $now,$now],
                [6,8,1,'关联人物', $now,$now],
                [7,9,4,'殉国', $now,$now],
                [8,1,7,'社会动员', $now,$now],
                [9,6,6,'报道', $now,$now],
                [10,9,5,'医疗支援', $now,$now],
            ]
        );

        // -------------------------
        // 5) 标签 tag + 关联
        // -------------------------
        $this->batchInsert('{{%tag}}',
            ['id','name','created_at','updated_at'],
            [
                [1,'战役', $now,$now],
                [2,'事变', $now,$now],
                [3,'殉国', $now,$now],
                [4,'外交', $now,$now],
                [5,'救援', $now,$now],
                [6,'报道', $now,$now],
                [7,'敌后战场', $now,$now],
                [8,'胜利', $now,$now],
            ]
        );

        // 事件-标签
        $this->batchInsert('{{%war_event_tag}}',
            ['event_id','tag_id'],
            [
                [1,2],
                [2,1],
                [3,4],
                [4,2],
                [5,1],
                [6,1],
                [7,3],
                [8,7],
                [10,8],
            ]
        );

        // 人物-标签
        $this->batchInsert('{{%war_person_tag}}',
            ['person_id','tag_id'],
            [
                [1,3],
                [2,3],
                [3,3],
                [4,3],
                [5,5],
                [6,6],
                [7,4],
            ]
        );

        // -------------------------
        // 6) war_message（留言：event/person 两种 target）
        // -------------------------
        $this->batchInsert('{{%war_message}}',
            ['id','nickname','content','target_type','target_id','status','created_at','updated_at'],
            [
                [1,'同学A','铭记历史，珍爱和平。','event',10,1,$now,$now],
                [2,'同学B','向所有抗战英烈致敬！','event',4,1,$now,$now],
                [3,'同学C','愿山河无恙，吾辈自强。','person',1,1,$now,$now],
                [4,'同学D','谢谢你们守护过这片土地。','person',4,1,$now,$now],
                [5,'匿名','致敬每一位普通民众。','person',8,0,$now,$now], // 待审核示例
                [6,'游客','勿忘国耻，吾辈自强。','event',7,0,$now,$now],   // 待审核示例
            ]
        );

        return true;
    }

    public function down()
    {
        // 按固定 ID 精准回滚，避免误删后续新增数据

        // war_message
        $this->delete('{{%war_message}}', ['id' => [1,2,3,4,5,6]]);

        // war_person_tag / war_event_tag
        $this->delete('{{%war_person_tag}}', ['person_id' => [1,2,3,4,5,6,7]]);
        $this->delete('{{%war_event_tag}}', ['event_id' => [1,2,3,4,5,6,7,8,10]]);

        // tag
        $this->delete('{{%tag}}', ['id' => [1,2,3,4,5,6,7,8]]);

        // war_event_person
        $this->delete('{{%war_event_person}}', ['id' => [1,2,3,4,5,6,7,8,9,10]]);

        // war_event / war_person / war_stage
        $this->delete('{{%war_event}}', ['id' => [1,2,3,4,5,6,7,8,9,10]]);
        $this->delete('{{%war_person}}', ['id' => [1,2,3,4,5,6,7,8]]);
        $this->delete('{{%war_stage}}', ['id' => [1,2]]);

        return true;
    }
}
