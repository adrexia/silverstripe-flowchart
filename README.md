#Silverstripe Flowchart

> Prototype for a graphical flowchart creator within Silverstripe

Currently in "proof of concept" stage. Does not yet do anything useful. Also, the code is more than a bit messy.

## Built with JSPlumb

http://jsplumbtoolkit.com/

## Approach
1. Enter data in a gridfield on the FlowchartPage type
2. Manually construct drawings with that data in a ModelAdmin

## TODO
* Save and reload flowcharts for editing
  * In a generic way (JSON), then in the CMS
* "Publish to" / Recreate saved flowcharts on the FE (minus the ability to edit)
* Override edit button on gridfield in Admin area to instead display the flowchart workspace
* "Tidy up"/ Rewrite the JS and CSS (cf proof of concept)
* Everything else that is needed to integrate this with the CMS

