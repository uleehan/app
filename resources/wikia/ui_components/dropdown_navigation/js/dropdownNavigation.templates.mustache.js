define( 'wikia.dropdownNavigation.templates', [], function() { 'use strict'; return {
    "dropdown_navigation" : '<ul id="{{id}}" class="wikia-dropdown-nav" style="{{#maxHeight}}max-height: {{maxHeight}}px;{{/maxHeight}}">{{#data}}<li><a href="{{href}}" title="{{title}}" {{#dataAttr}}data-{{key}}="{{value}}"{{/dataAttr}} rel="nofollow" {{#class}} class="{{class}}" {{/class}}>{{title}}</a></li>{{/data}}</ul>',
    "done": "true"
  }; });
