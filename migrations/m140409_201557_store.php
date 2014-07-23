<?php

class m140409_201557_store extends DbMigration
{
	public function up()
	{
        $this->createTable('{{product}}',[
            'id'=>'pk',
            'name'=>'VARCHAR(255)',
            'uid'=>'VARCHAR(255)',
            'alias'=>'VARCHAR(255)',
            'article'=>'VARCHAR(255)',
            'enabled'=>'TINYINT NOT NULL DEFAULT 0',
            'unit'=>'VARCHAR(20)',
            'content'=>'TEXT',
            'short_content'=>'TEXT',
            'category_id'=>'INT',
            'manufacturer_id'=>'INT',
            'image_id'=>'INT',
            'remains'=>'INT UNSIGNED DEFAULT 0',
            'remains_warning'=>'INT UNSIGNED DEFAULT 0',
            'out_of_stock_counter'=>'INT UNSIGNED DEFAULT 0',
            'price'=>'DECIMAL(10,2) NOT NULL DEFAULT 0',
            'status'=>'TINYINT NOT NULL DEFAULT 0',
			'featured'=>'TINYINT NOT NULL DEFAULT 0',
            'action_enabled'=>'TINYINT NOT NULL DEFAULT 0',
            'action_start'=>'DATE',
            'action_end'=>'DATE',
            'action_price'=>'DECIMAL(10,2) NOT NULL DEFAULT 0',
			'sort'=>'INT NOT NULL DEFAULT 0',
            'create_time'=>'DATETIME',
            'update_time'=>'DATETIME',
        ]);
		 
        $this->createIndex('product_category_idx','{{product}}','category_id');
        $this->createIndex('product_manufacturer_idx','{{product}}','manufacturer_id');

        $this->createTable('{{product_category}}',[
            'id'=>'pk',
            'name'=>'VARCHAR(255)',
            'uid'=>'VARCHAR(255)',
            'alias'=>'VARCHAR(255)',
            'parent_id'=>'INT NOT NULL DEFAULT 0',
            'image_id'=>'INT',
            'content'=>'TEXT',
            'short_content'=>'TEXT',
            'sort'=>'INT NOT NULL DEFAULT 0',
            'enabled'=>'TINYINT NOT NULL DEFAULT 0',
            'status'=>'TINYINT NOT NULL DEFAULT 0',
            'remains_warning'=>'INT UNSIGNED DEFAULT 0',
            'create_time'=>'DATETIME',
            'update_time'=>'DATETIME',
        ]);
        $this->createIndex('parent_category_idx','{{product_category}}','parent_id');
        $this->createIndex('parent_category_sort','{{product_category}}','parent_id, sort');

        $this->createTable('{{product_filter}}',[
            'id'=>'pk',
            'name'=>'VARCHAR(255)',
            'key'=>'VARCHAR(255)',
            'type'=>'TINYINT NOT NULL',
            'data'=>'TEXT',
        ]);

        $this->createIndex('pf_keyunique','{{product_filter}}','key',true);

        $this->createTable('{{product_filter_value}}',[
            'id'=>'pk',
            'filter_id'=>'INT NOT NULL',
            'name'=>'VARCHAR(255)',
            'key'=>'VARCHAR(255)',
        ]);

        $this->createIndex('pfv_fidx','{{product_filter_value}}','filter_id');
        $this->createIndex('pfv_fidkey_unique','{{product_filter_value}}','filter_id, key', true);

        $this->createTable('{{product_to_filter_value}}', [
            'id'=>'pk',
            'product_id'=>'INT NOT NULL',
            'value_id'=>'INT NOT NULL',
        ]);
        $this->createIndex('ptfv_product_idx','{{product_to_filter_value}}','product_id');
        $this->createIndex('ptfv_value_idx','{{product_to_filter_value}}','value_id');

        $this->createTable('{{product_category_to_filter}}', [
            'id'=>'pk',
            'category_id'=>'INT NOT NULL',
            'filter_id'=>'INT NOT NULL',
        ]);
        $this->createIndex('ftc_category_idx','{{product_category_to_filter}}','category_id');
        $this->createIndex('ftc_filter_idx','{{product_category_to_filter}}','filter_id');

        $this->createTable('{{product_manufacturer}}', [
            'id'=>'pk',
            'name'=>'VARCHAR(255) NOT NULL',
            'short_name'=>'VARCHAR(255) NOT NULL',
            'alias'=>'VARCHAR(255)',
            'image_id'=>'INT',
            'content'=>'TEXT',
            'short_content'=>'TEXT',
        ]);

        $this->createTable('{{product_related}}', [
            'id'=>'pk',
            'entity'=>'VARCHAR(255) NOT NULL',
            'entity_id'=>'INT NOT NULL',
            'product_id'=>'INT NOT NULL',
            'meta'=>'TEXT',
            'sort'=>'INT',
        ]);
        $this->createIndex('product_related_index','{{product_related}}','entity, entity_id, product_id',true);
		
		        if (!db()->getSchema()->getTable('{{product_manufacturer_lang}}')) {
            $this->createTable('{{product_manufacturer_lang}}', [
                'l_id' => 'pk',
                'entity_id' => 'INT NOT NULL',
                'lang_id' => 'VARCHAR(6) NOT NULL',
                'l_name' => 'VARCHAR(255)',
                'l_short_name' => 'VARCHAR(255)',
            ]);
            $this->createIndex('pml_ei', '{{product_manufacturer_lang}}', 'entity_id');
            $this->createIndex('pml_li', '{{product_manufacturer_lang}}', 'lang_id');

            $this->addForeignKey('pml_ibfk_1', '{{product_manufacturer_lang}}', 'entity_id', '{{product_manufacturer}}', 'id', 'CASCADE', 'CASCADE');
        }

        if (!db()->getSchema()->getTable('{{product_category_lang}}')) {
            $this->createTable('{{product_category_lang}}', [
                'l_id' => 'pk',
                'entity_id' => 'INT NOT NULL',
                'lang_id' => 'VARCHAR(6) NOT NULL',
                'l_name' => 'VARCHAR(255)',
                'l_content' => 'TEXT',
                'l_short_content' => 'TEXT',
            ]);
            $this->createIndex('pc_ei', '{{product_category_lang}}', 'entity_id');
            $this->createIndex('pc_li', '{{product_category_lang}}', 'lang_id');

            $this->addForeignKey('pc_ibfk_1', '{{product_category_lang}}', 'entity_id', '{{product_category}}', 'id', 'CASCADE', 'CASCADE');
        }

        if (!db()->getSchema()->getTable('{{product_filter_lang}}')) {
            $this->createTable('{{product_filter_lang}}', [
                'l_id' => 'pk',
                'entity_id' => 'INT NOT NULL',
                'lang_id' => 'VARCHAR(6) NOT NULL',
                'l_name' => 'VARCHAR(255)',
            ]);
            $this->createIndex('pf_ei', '{{product_filter_lang}}', 'entity_id');
            $this->createIndex('pf_li', '{{product_filter_lang}}', 'lang_id');

            $this->addForeignKey('pf_ibfk_1', '{{product_filter_lang}}', 'entity_id', '{{product_filter}}', 'id', 'CASCADE', 'CASCADE');
        }

        if (!db()->getSchema()->getTable('{{product_filter_value_lang}}')) {
            $this->createTable('{{product_filter_value_lang}}', [
                'l_id' => 'pk',
                'entity_id' => 'INT NOT NULL',
                'lang_id' => 'VARCHAR(6) NOT NULL',
                'l_name' => 'VARCHAR(255)',
            ]);
            $this->createIndex('pfv_ei', '{{product_filter_value_lang}}', 'entity_id');
            $this->createIndex('pfv_li', '{{product_filter_value_lang}}', 'lang_id');

            $this->addForeignKey('pfv_ibfk_1', '{{product_filter_value_lang}}', 'entity_id', '{{product_filter_value}}', 'id', 'CASCADE', 'CASCADE');
        }

        if (!db()->getSchema()->getTable('{{product_lang}}')) {
            $this->createTable('{{product_lang}}', [
                'l_id' => 'pk',
                'entity_id' => 'INT NOT NULL',
                'lang_id' => 'VARCHAR(6) NOT NULL',
                'l_name' => 'VARCHAR(255)',
                'l_content' => 'TEXT',
                'l_short_content' => 'TEXT',
            ]);
            $this->createIndex('p_ei', '{{product_lang}}', 'entity_id');
            $this->createIndex('p_li', '{{product_lang}}', 'lang_id');

            $this->addForeignKey('p_ibfk_1', '{{product_lang}}', 'entity_id', '{{product}}', 'id', 'CASCADE', 'CASCADE');
        }
		
		
    }

	public function down()
	{
	     $this->dropTable('{{product_lang}}');

        $this->dropTable('{{product_filter_value_lang}}');

        $this->dropTable('{{product_filter_lang}}');

        $this->dropTable('{{product_category_lang}}');

        $this->dropTable('{{product_manufacturer_lang}}');
	
	
	
        $this->dropIndex('product_related_index','{{product_related}}');
        $this->dropTable('{{product_related}}');

        $this->dropTable('{{product_manufacturer}}');

        $this->dropIndex('ftc_category_idx','{{product_category_to_filter}}');
        $this->dropIndex('ftc_filter_idx','{{product_category_to_filter}}');

        $this->dropTable('{{product_category_to_filter}}');

        $this->dropIndex('ptfv_product_idx','{{product_to_filter_value}}');
        $this->dropIndex('ptfv_value_idx','{{product_to_filter_value}}');

        $this->dropTable('{{product_to_filter_value}}');

        $this->dropIndex('pfv_fidx','{{product_filter_value}}');
        $this->dropIndex('pfv_fidkey_unique','{{product_filter_value}}');
        $this->dropTable('{{product_filter_value}}');

        $this->dropIndex('pf_keyunique','{{product_filter}}');
        $this->dropTable('{{product_filter}}');

        $this->dropIndex('parent_category_idx','{{product_category}}');
        $this->dropIndex('parent_category_sort','{{product_category}}');

        $this->dropTable('{{product_category}}');

        $this->dropIndex('product_category_idx','{{product}}');
        $this->dropIndex('product_manufacturer_idx','{{product}}');

        $this->dropTable('{{product}}');

	}

}