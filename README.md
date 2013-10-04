#Silverstripe Flowchart

> Prototype for a graphical flowchart creator within Silverstripe

Implementation of JSPlumb to create and store graphical flowcharts within SilverStripe

## Built with JSPlumb

http://jsplumbtoolkit.com/

## Approach
1. Enter data in a gridfield on the FlowchartPage type
2. Manually construct drawings with that data in a ModelAdmin

## TODO
* ~~Save and reload flowcharts for editing~~
  * ~~In a generic way (JSON), then in the CMS~~
* ~~"Publish to" / Recreate saved flowcharts on the FE (minus the ability to edit)~~
* ~~Override edit button on gridfield in Admin area to instead display the flowchart workspace~~
* "Tidy up"/ Rewrite the JS and CSS (cf proof of concept)
* Better user feedback in the CMS around saving a graphical flowchart (change button names, add save and publish)
* A better way to link to other states (treedropdown, or filtered somehow...)
* ~Tighten up drag and drop from new states panel. ~
* ~Min width on cms admin (unavoidable :( )~
* ~Make design more generic~
* ~Fix title issue (getTitle)~

##Screen Shots

![](images/constructed-chart.png)
![](images/chart.png)

