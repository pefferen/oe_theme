!function(e,t){"object"==typeof exports&&"object"==typeof module?module.exports=t():"function"==typeof define&&define.amd?define([],t):"object"==typeof exports?exports.CKEditor5=t():(e.CKEditor5=e.CKEditor5||{},e.CKEditor5.tableZebraStriping=t())}(self,(()=>(()=>{var e={"ckeditor5/src/core.js":(e,t,r)=>{e.exports=r("dll-reference CKEditor5.dll")("./src/core.js")},"ckeditor5/src/ui.js":(e,t,r)=>{e.exports=r("dll-reference CKEditor5.dll")("./src/ui.js")},"dll-reference CKEditor5.dll":e=>{"use strict";e.exports=CKEditor5.dll}},t={};function r(i){var o=t[i];if(void 0!==o)return o.exports;var n=t[i]={exports:{}};return e[i](n,n.exports,r),n.exports}r.d=(e,t)=>{for(var i in t)r.o(t,i)&&!r.o(e,i)&&Object.defineProperty(e,i,{enumerable:!0,get:t[i]})},r.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t);var i={};return(()=>{"use strict";r.d(i,{default:()=>d});var e=r("ckeditor5/src/core.js"),t=r("ckeditor5/src/ui.js");class o extends e.Plugin{static get pluginName(){return"TableZebraStripingUi"}init(){const r=this.editor;r.ui.componentFactory.add("toggleTableZebraStriping",(i=>{const o=r.commands.get("toggleTableZebraStriping"),n=new t.ButtonView(i);return n.set({icon:e.icons.cancel,tooltip:!0,isToggleable:!0}),n.bind("isOn","isEnabled").to(o,"value","isEnabled"),n.bind("label").to(o,"value",(e=>e?Drupal.t("Toggle zebra striping off"):Drupal.t("Toggle zebra striping on"))),this.listenTo(n,"execute",(()=>{r.execute("toggleTableZebraStriping"),r.editing.view.focus()})),n}))}}function n(e){const t=e.getSelectedElement();return t&&t.is("element","table")?t:e.getFirstPosition().findAncestor("table")}class s extends e.Command{refresh(){const e=n(this.editor.model.document.selection);this.isEnabled=!!e,this.isEnabled?this.value=e.hasAttribute("zebraStriping"):this.value=!1}execute(){const e=this.editor.model,t=n(e.document.selection);e.change((e=>{this.value?e.removeAttribute("zebraStriping",t):e.setAttribute("zebraStriping","true",t)}))}}class a extends e.Plugin{init(){const e=this.editor,t=e.model.schema,r=e.conversion;t.extend("table",{allowAttributes:"zebraStriping"}),r.for("upcast").attributeToAttribute({view:{name:"table",key:"data-striped"},model:"zebraStriping"}),r.for("editingDowncast").attributeToAttribute({model:{name:"table",key:"zebraStriping"},view:{key:"class",value:["table-zebra-striped"]}}),r.for("dataDowncast").attributeToAttribute({model:{name:"table",key:"zebraStriping"},view:"data-striped"}),e.commands.add("toggleTableZebraStriping",new s(e))}}class l extends e.Plugin{static get requires(){return[a,o]}}const d={TableZebraStriping:l}})(),i=i.default})()));