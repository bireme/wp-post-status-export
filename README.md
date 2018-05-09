# WP Post Status Export

## About
This plugin show up the WP post status in output format (XML and JSON).

## How to use
Activate the plugin and access `http://<domain>/?feed=stats` to show the output file.

You can also use the following parameters and filters:

* `post_type`- Filter by post type (default: post)
* `format`-  Output format. Possible values are __xml__ and __json__ (default: xml)

## Usage example
```
http://<domain>/?feed=stats&post_type=post&format=xml
```

## Output example
#### XML
```
<stats>
    <total>111</total>
    <status>
        <field name="publish">100</field>
        <field name="draft">10</field>
        <field name="pending">1</field>
    </status>
</stats>
```
#### JSON
```
{
    total: 111,
    status: {
        publish: "100",
        draft: "10",
        pending: "1",
    },
}
```
