<?php

$_['types'] = [
    'product',
    'attribute',
    'attribute_group',
    'blog_post',
    'category',
    'download',
    'filter',
    'information',
    'option',
    'recurring',
    'subscription_plans'
];

$_['events'] = [
    'admin/view/common/header/after' => 'extension/auto_content_translator/event/auto_content_translator|view_common_header_after',
    'admin/view/catalog/category_form/after' => 'extension/auto_content_translator/event/auto_content_translator|view_catalog_category_form_after',
    'admin/view/catalog/product_form/after' => 'extension/auto_content_translator/event/auto_content_translator|view_catalog_product_form_after',
    'admin/view/catalog/option_form/after' => 'extension/auto_content_translator/event/auto_content_translator|view_catalog_option_form_after',
    'admin/view/catalog/information_form/after' => 'extension/auto_content_translator/event/auto_content_translator|view_catalog_information_form_after',
    'admin/view/extension/d_blog_module/post_form/after' => 'extension/auto_content_translator/event/auto_content_translator|view_extension_d_blog_module_post_form_after',
    'admin/controller/common/header/before' => 'extension/auto_content_translator/event/auto_content_translator|controller_common_header_before'
];

$_['url_google'] = 'https://www.googleapis.com/language/translate/v2';

$_['google_codes'] = [
    'ar', 'az', 'be', 'bg', 'bn', 'ca', 'cs', 'cy', 'da', 'de', 'el', 'en', 'eo', 'et', 'es', 'eu', 'fa', 'fi', 'fr', 'ga', 'gl', 
    'gu', 'hi', 'hr', 'ht', 'hu', 'id', 'is', 'it', 'iw', 'ja', 'ka', 'kn', 'ko', 'la', 'lt', 'lv', 'mk', 'ms', 'mt', 'nl', 'no',
    'pl', 'pt', 'ro', 'ru', 'sk',  'sl', 'sq', 'sr', 'sv', 'sw', 'ta', 'te', 'th',  'tl',  'tr', 'uk', 'ur', 'vi', 'yi' ,'zh-CN', 'zh-TW'
];

$_['translation_fields'] = [
    'category' => [
        'name' => [
            'enabled' => 1, 
            'title' => 'Name'
        ],
        'description' => [
            'enabled' => 1, 
            'title' => 'Description'
        ],
        'meta_title' => [
            'enabled' => 1, 
            'title' => 'Meta Title'
        ],
        'meta_description' => [
            'enabled' => 1, 
            'title' => 'Meta Description'
        ],
        'meta_keyword' => [
            'enabled' => 1, 
            'title' => 'Meta Keywords'
        ],
    ],
    'option' => [
        'name' => [
            'enabled' => 1, 
            'title' => 'Name'
        ],
        'values' => [
            'enabled' => 1, 
            'title' => 'Values'
        ]
    ],
    'product' => [
        'name' => [
            'enabled' => 1, 
            'title' => 'Name'
        ],
        'description' => [
            'enabled' => 1, 
            'title' => 'Description'
        ],
        'meta_title' => [
            'enabled' => 1, 
            'title' => 'Meta Title'
        ],
        'meta_description' => [
            'enabled' => 1, 
            'title' => 'Meta Description'
        ],
        'meta_keyword' => [
            'enabled' => 1, 
            'title' => 'Meta Keywords'
        ],
        'tag' => [
            'enabled' => 1, 
            'title' => 'Tags'
        ]
    ],
    'information' => [
        'title' => [
            'enabled' => 1, 
            'title' => 'Title'
        ],
        'description' => [
            'enabled' => 1, 
            'title' => 'Description'
        ],
        'meta_title' => [
            'enabled' => 1, 
            'title' => 'Meta Title'
        ],
        'meta_description' => [
            'enabled' => 1, 
            'title' => 'Meta Description'
        ],
        'meta_keyword' => [
            'enabled' => 1, 
            'title' => 'Meta Keywords'
        ]
    ],
    'blog_post' => [
        'title' => [
            'enabled' => 1, 
            'title' => 'Title'
        ],
        'short_description' => [
            'enabled' => 1, 
            'title' => 'Short Description'
        ],
        'description' => [
            'enabled' => 1, 
            'title' => 'Description'
        ],
        'meta_title' => [
            'enabled' => 1, 
            'title' => 'Meta Title'
        ],
        'meta_description' => [
            'enabled' => 1, 
            'title' => 'Meta Description'
        ],
        'meta_keyword' => [
            'enabled' => 1, 
            'title' => 'Meta Keywords'
        ],
        'tag' => [
            'enabled' => 1, 
            'title' => 'Tags'
        ]
    ],
    'attribute' => [
        'name' => [
            'enabled' => 1, 
            'title' => 'Name'
        ]
    ],
    'attribute_group' => [
        'name' => [
            'enabled' => 1, 
            'title' => 'Name'
        ]
    ],
    'download' => [
        'name' => [
            'enabled' => 1, 
            'title' => 'Name'
        ]
    ],
    'filter' => [
        'name' => [
            'enabled' => 1, 
            'title' => 'Name'
        ]
    ],
    'recurring' => [
        'name' => [
            'enabled' => 1, 
            'title' => 'Name'
        ]
    ],
    'subscription_plans' => [
        'name' => [
            'enabled' => 1, 
            'title' => 'Name'
        ]
    ],
];

$_['fields'] = [
    'article_description[@][title]',
    'article_description[@][meta_title]',
    'article_description[@][description]',
    'article_description[@][meta_description]',
    'article_description[@][meta_keyword]',
    'article_tag[@]',
    'attribute_description[@][name]',
    'attribute_group_description[@][name]',
    '[banner_image_description][@][title]',
    'category_description[@][name]',
    'category_description[@][meta_description]',
    'category_description[@][meta_keyword]',
    'category_description[@][description]',
    'customer_group_description[@][name]',
    'customer_group_description[@][description]',
    'download_description[@][name]',
    'filter_group_description[@][name]',
    '[filter_description][@][name]',
    'information_description[@][title]',
    'information_description[@][description]',
    'information_description[@][meta_title]',
    'information_description[@][meta_description]',
    'information_description[@][meta_keyword]',
    'information_description[@][name]',
    'recurring_description[@][name]',
    'length_class_description[@][unit]',
    'weight_class_description[@][title]',
    'weight_class_description[@][unit]',
    'option_description[@][name]',
    '[option_value_description][@][name]',
    'order_status[@][name]',
    'product_description[@][name]', 
    'product_description[@][alternative_name]', 
    'product_description[@][meta_title]', 
    'product_description[@][meta_description]', 
    'product_description[@][meta_keyword]', 
    'product_description[@][description]', 
    'product_description[@][tag]',
    '[product_attribute_description][@][text]',
    'profile_description[@][name]',
    'return_action[@][name]',
    'return_reason[@][name]',
    'return_status[@][name]',
    'stock_status[@][name]',
    'voucher_theme_description[@][name]',
    'post_description[@][title]', 
    'post_description[@][short_description]', 
    'post_description[@][description]', 
    'post_description[@][meta_title]', 
    'post_description[@][meta_description]', 
    'post_description[@][meta_keyword]', 
    'post_description[@][tag]',
    'subscription_plan_description[@][name]'
];
