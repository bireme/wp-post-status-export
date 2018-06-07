# WP Post Status Export

## About
This plugin show up the WP post status in output format (XML and JSON).

## How to use
Activate the plugin and access `http://<domain>/?feed=stats` to show the output file.

You can also use the following parameters and filters:

* `post_type`- Filter by post type (default: post)
* `tax`- Filter by taxonomy slug
* `poll`- Filter by poll ID
* `initial_date`- Filter by publish date (format: YYYYMMDD or YYYY-MM-DD)
* `end_date`- Filter by publish date (format: YYYYMMDD or YYYY-MM-DD)
* `count`- Display content limit (default: -1 = ALL)
* `format`- Output format. Possible values are __xml__ and __json__ (default: xml)

## Usage example
```
http://<domain>/?feed=stats&post_type=post&tax=teleconsultor&format=xml
```

## NOTES
* The __initial_date__ and __end_date__ parameters are dependent
* The __initial_date__ and __end_date__ parameters only work if __poll__ parameter is not set
* The __poll__ parameter only works if [YOP Poll plugin](https://wordpress.org/plugins/yop-poll/) is activated

## Output example
#### XML
```
<stats>
    <total>111</total>
    <status>
        <field name="publish">100</field>
        <field name="draft">10</field>
        <field name="pending">5</field>
    </status>
    <taxonomy>
        <item>
            <name><![CDATA[ITEM 1]]></name>
            <total>30</total>
            <status>
                <field name="publish">29</field>
                <field name="pending">2</field>
                <field name="draft">1</field>
            </status>
        </item>
        <item>
            <name><![CDATA[ITEM 2]]></name>
            <total>60</total>
            <status>
                <field name="publish">57</field>
                <field name="draft">3</field>
                <field name="pending">1</field>
            </status>
        </item>
    </taxonomy>
</stats>
```
#### JSON
```
{
    total: 111,
    status: {
        publish: "100",
        draft: "10",
        pending: "5",
    },
    taxonomy: [
        {
            name: "ITEM 1",
            total: 30,
            status: {
                publish: 29,
                pending: 2,
                draft: 1,
            },
        },
        {
            name: "ITEM 2",
            total: 60,
            status: {
                publish: 57,
                draft: 3,
                pending: 1,
            },
        },
    ],
}
```
