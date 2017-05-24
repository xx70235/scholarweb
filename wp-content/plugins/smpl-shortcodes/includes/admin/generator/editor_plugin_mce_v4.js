(function(){

	var icon_url = '/includes/admin/generator/assets/images/smpl-icon.png';
	tinymce.create( "tinymce.plugins.SmplShortcodes",{
			SmplShortcodes: function(d,e) {

				d.addCommand( "smplOpenDialog",function(a,c){

					// Grab the selected text from the content editor.
					selectedText = '';
					if ( d.selection.getContent().length > 0 ) {
						selectedText = d.selection.getContent();
					} // End IF Statement

					smplSelectedShortcodeType = c.identifier;
					smplSelectedShortcodeTitle = c.title;

					jQuery.get(e+"/dialog.php",function(b){
						var a;
						jQuery('#smpl-shortcode-options').addClass( 'shortcode-' + smplSelectedShortcodeType );
						// Skip the popup on certain shortcodes.
						switch ( smplSelectedShortcodeType ) {
							// -------------------------------------------------------------
							// Escape Auto WP
							// -------------------------------------------------------------
							// raw
							case 'raw':
							a = '[raw]'+selectedText+'[/raw]';
							tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
							break;

							// -------------------------------------------------------------
							// Call to Action Box
							// -------------------------------------------------------------

							// cta
							case 'cta':
							a = '[cta]'+selectedText+'[/cta]';
							tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
							break;

							// -------------------------------------------------------------
							// 2 Columns
							// -------------------------------------------------------------

							// 50% | 50%
							case '2-col-50-50':
							a = '[one_half] content... [/one_half]<br />';
							a += '[one_half last] content... [/one_half]<br />';
							a += '[clear]';
							tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
							break;

							// 25% | 75%
							case '2-col-25-75':
							a = '[one_fourth]content...[/one_fourth]<br />';
							a += '[three_fourth last]content...[/three_fourth]<br />';
							a += '[clear]<br />';
							tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
							break;

							// 75% | 25%
							case '2-col-75-25':
							a = '[three_fourth]content...[/three_fourth]<br />';
							a += '[one_fourth last]content...[/one_fourth]<br />';
							a += '[clear]<br />';
							tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
							break;

							// 33% | 66%
							case '2-col-33-66':
							a = '[one_third]content...[/one_third]<br />';
							a += '[two_third last]content...[/two_third]<br />';
							a += '[clear]<br />';
							tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
							break;

							// 66% | 33%
							case '2-col-66-33':
							a = '[two_third]content...[/two_third]<br />';
							a += '[one_third last]content...[/one_third]<br />';
							a += '[clear]<br />';
							tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
							break;

							// 20% | 80%
							case '2-col-20-80':
							a = '[one_fifth]content...[/one_fifth]<br />';
							a += '[four_fifth last]content...[/four_fifth]<br />';
							a += '[clear]<br />';
							tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
							break;

							// 80% | 20%
							case '2-col-80-20':
							a = '[four_fifth]content...[/four_fifth]<br />';
							a += '[one_fifth last]content...[/one_fifth]<br />';
							a += '[clear]<br />';
							tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
							break;

							// -------------------------------------------------------------
							// 3 Columns
							// -------------------------------------------------------------

							// 33% | 33% | 33%
							case '3-col-33-33-33':
							a = '[one_third]content...[/one_third]<br />';
							a += '[one_third]content...[/one_third]<br />';
							a += '[one_third last]content...[/one_third]<br />';
							a += '[clear]<br />';
							tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
							break;

							// 25% | 25% | 50%
							case '3-col-25-25-50':
							a = '[one_fourth]content...[/one_fourth]<br />';
							a += '[one_fourth]content...[/one_fourth]<br />';
							a += '[one_half last]content...[/one_half]<br />';
							a += '[clear]<br />';
							tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
							break;

							// 25% | 50% | 25%
							case '3-col-25-50-25':
							a = '[one_fourth]content...[/one_fourth]<br />';
							a += '[one_half]content...[/one_half]<br />';
							a += '[one_fourth last]content...[/one_fourth]<br />';
							a += '[clear]<br />';
							tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
							break;

							// 50% | 25% | 25%
							case '3-col-50-25-25':
							a = '[one_half]content...[/one_half]<br />';
							a += '[one_fourth]content...[/one_fourth]<br />';
							a += '[one_fourth last]content...[/one_fourth]<br />';
							a += '[clear]<br />';
							tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
							break;

							// 20% | 20% | 60%
							case '3-col-20-20-60':
							a = '[one_fifth]content...[/one_fifth]<br />';
							a += '[one_fifth]content...[/one_fifth]<br />';
							a += '[three_fifth last]content...[/three_fifth]<br />';
							a += '[clear]<br />';
							tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
							break;

							// 20% | 60% | 20%
							case '3-col-20-60-20':
							a = '[one_fifth]content...[/one_fifth]<br />';
							a += '[three_fifth]content...[/three_fifth]<br />';
							a += '[one_fifth last]content...[/one_fifth]<br />';
							a += '[clear]<br />';
							tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
							break;

							// 60% | 20% | 20%
							case '3-col-60-20-20':
							a = '[three_fifth]content...[/three_fifth]<br />';
							a += '[one_fifth]content...[/one_fifth]<br />';
							a += '[one_fifth last]content...[/one_fifth]<br />';
							a += '[clear]<br />';
							tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
							break;

							// -------------------------------------------------------------
							// 4 Columns
							// -------------------------------------------------------------

							// 25% | 25% | 25% | 25%
							case '4-col-25-25-25-25':
							a = '[one_fourth]content...[/one_fourth]<br />';
							a += '[one_fourth]content...[/one_fourth]<br />';
							a += '[one_fourth]content...[/one_fourth]<br />';
							a += '[one_fourth last]content...[/one_fourth]<br />';
							a += '[clear]<br />';
							tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
							break;

							// 20% | 20% | 20% | 40%
							case '4-col-20-20-20-40':
							a = '[one_fifth]content...[/one_fifth]<br />';
							a += '[one_fifth]content...[/one_fifth]<br />';
							a += '[one_fifth]content...[/one_fifth]<br />';
							a += '[two_fifth last]content...[/two_fifth]<br />';
							a += '[clear]<br />';
							tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
							break;

							// 20% | 20% | 40% | 20%
							case '4-col-20-20-40-20':
							a = '[one_fifth]content...[/one_fifth]<br />';
							a += '[one_fifth]content...[/one_fifth]<br />';
							a += '[two_fifth]content...[/two_fifth]<br />';
							a += '[one_fifth last]content...[/one_fifth]<br />';
							a += '[clear]<br />';
							tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
							break;

							// 20% | 40% | 20% | 20%
							case '4-col-20-40-20-20':
							a = '[one_fifth]content...[/one_fifth]<br />';
							a += '[two_fifth]content...[/two_fifth]<br />';
							a += '[one_fifth]content...[/one_fifth]<br />';
							a += '[one_fifth last]content...[/one_fifth]<br />';
							a += '[clear]<br />';
							tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
							break;

							// 40% | 20% | 20% | 20%
							case '4-col-40-20-20-20"':
							a = '[two_fifth]content...[/two_fifth]<br />';
							a += '[one_fifth]content...[/one_fifth]<br />';
							a += '[one_fifth]content...[/one_fifth]<br />';
							a += '[one_fifth last]content...[/one_fifth]<br />';
							a += '[clear]<br />';
							tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
							break;

							// -------------------------------------------------------------
							// 5 Columns
							// -------------------------------------------------------------

							// 20% | 20% | 20% | 20% | 20%
							case '5-col-20-20-20-20-20':
							a = '[one_fifth]content...[/one_fifth]<br />';
							a += '[one_fifth]content...[/one_fifth]<br />';
							a += '[one_fifth]content...[/one_fifth]<br />';
							a += '[one_fifth]content...[/one_fifth]<br />';
							a += '[one_fifth last]content...[/one_fifth]<br />';
							a += '[clear]<br />';
							tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
							break;

							// -------------------------------------------------------------
							// 6 Columns
							// -------------------------------------------------------------

							// 15% | 15% | 15% | 15% | 15% | 15%
							case '6-col-15-15-15-15-15-15':
							a = '[one_sixth]content...[/one_sixth]<br />';
							a += '[one_sixth]content...[/one_sixth]<br />';
							a += '[one_sixth]content...[/one_sixth]<br />';
							a += '[one_sixth]content...[/one_sixth]<br />';
							a += '[one_sixth]content...[/one_sixth]<br />';
							a += '[one_sixth last]content...[/one_sixth]<br />';
							a += '[clear]<br />';
							tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
							break;

							// -------------------------------------------------------------
							// Tabs
							// -------------------------------------------------------------

							// tabs
							case 'tabs':
							a  = '[tabgroup]<br />';
							a += '[tab title="Tab 1" id="t1"]Tab 1 content[/tab]<br />';
							a += '[tab title="Tab 2" id="t2"]Tab 2 content[/tab]<br />';
							a += '[tab title="Tab 3" id="t3"]Tab 3 content[/tab]<br />';
							a += '[/tabgroup]<br />';
							tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
							break;

							// -------------------------------------------------------------
							// Accordion
							// -------------------------------------------------------------

							// accordion
							case 'accordion':
							a = '[accordion]<br />';
							a += '[toggle title="Toggle #1"]Your content goes here.[/toggle]<br />';
							a += '[toggle title="Toggle #2"]Your content goes here.[/toggle]<br />';
							a += '[toggle title="Toggle #3"]Your content goes here.[/toggle]<br />';
							a += '[/accordion]<br />';
							tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
							break;

							default:

							jQuery("#smpl-dialog").remove();
							jQuery("body").append(b);
							jQuery("#smpl-dialog").hide();
							var f =jQuery("#TB_window").width();
							b = jQuery(window).height() - 30;
							f = 720 < f ? 720 : f;
							f -= 80;
							b -= 104;

							tb_show("Insert SMPL "+ smplSelectedShortcodeTitle +" Shortcode", "#TB_inline?width="+f+"&height="+b+"&inlineId=smpl-dialog");jQuery("#smpl-shortcode-options h3:first").text(""+c.title+" Shortcode Settings");

							break;

						} // End SWITCH Statement
					}) // end .get
				}); // end d.addComand

			}, // end init
			init:function(d,e) {

				var a=this;

				d.addButton('smpl_shortcodes_button', {
					type: 'menubutton',
				    text: null,
				    padding: '0 0 0 0',
				    margin: '0 0 0 0',
				    icon: 'smpl-shortcodes',
				    tooltip: 'Insert Shortcode',
					menu: [
		                {
		                	text: 'Video Elements',
		                	menu: [
		                		{
		                			text: 'YouTube Video',
		                			onclick: function() {
		                				//d.insertContent('Menu item 1');
		                				tinyMCE.activeEditor.execCommand("smplOpenDialog", false, { title : 'YouTube Video', identifier : 'youtube' })
		                			}
		                		},
								{
				                	text: 'Vimeo Video',
				                	onclick: function() {
				                		//d.insertContent('Menu item 1');
				                		tinyMCE.activeEditor.execCommand("smplOpenDialog", false, { title : 'Vimeo Video', identifier : 'vimeo' })
				                	}
				                },
		                	],
		                },
		                {
		                	text: 'Components',
		                	menu: [
		                		{
				                	text: 'Alert',
				                	onclick: function() {
				                		//d.insertContent('Menu item 1');
				                		tinyMCE.activeEditor.execCommand("smplOpenDialog", false, { title : 'Alert', identifier : 'alert' })
				                	}
			                	},
				                {
				                	text: 'Callout',
				                	onclick: function() {
				                		//d.insertContent('Menu item 1');
				                		tinyMCE.activeEditor.execCommand("smplOpenDialog", false, { title : 'Callout', identifier : 'callout' })
				                	}
				                },
				                {
				                	text: 'Button',
				                	onclick: function() {
				                		//d.insertContent('Menu item 1');
				                		tinyMCE.activeEditor.execCommand("smplOpenDialog", false, { title : 'Button', identifier : 'button' })
				                	}
				                },
		                	]
		                },
		                {
							text: 'Interactive',
							menu: [
								{
									text: 'Tab Group',
									onclick: function() {
										//d.insertContent('Menu item 1');
										tinyMCE.activeEditor.execCommand("smplOpenDialog", false, { title : e, identifier : 'tabs' })
									}
								},
								{
									text: 'Toggle Group',
									onclick: function() {
										//d.insertContent('Menu item 2');
										tinyMCE.activeEditor.execCommand("smplOpenDialog", false, { title : e, identifier : 'accordion' })
									}
								},
								{
									text: 'Single Toggle',
									onclick: function() {
										//d.insertContent('Menu item 1');
										tinyMCE.activeEditor.execCommand("smplOpenDialog", false, { title : 'Button', identifier : 'toggle' })
									}
								},
							]
						},
		                {
							text: 'Columns',
							menu: [
								{
									text: '50% | 50%',
									onclick: function() {
										//d.insertContent('Menu item 1');
										tinyMCE.activeEditor.execCommand("smplOpenDialog", false, { title : e, identifier : '2-col-50-50' })
									}
								},
								{
									text: '33% | 33% | 33%',
									onclick: function() {
										//d.insertContent('Menu item 2');
										tinyMCE.activeEditor.execCommand("smplOpenDialog", false, { title : e, identifier : '3-col-33-33-33' })
									}
								},
								{
									text: '25% | 50% | 25%',
									onclick: function() {
										//d.insertContent('Menu item 1');
										tinyMCE.activeEditor.execCommand("smplOpenDialog", false, { title : '3 Columns', identifier : '3-col-25-50-25' })
									}
								},
								{
									text: '25% | 25% | 25% | 25%',
									onclick: function() {
										//d.insertContent('Menu item 1');
										tinyMCE.activeEditor.execCommand("smplOpenDialog", false, { title : '4 Columns', identifier : '4-col-25-25-25-25' })
									}
								},
								{
									text: '20% | 20% | 20% | 20% | 20%',
									onclick: function() {
										//d.insertContent('Menu item 1');
										tinyMCE.activeEditor.execCommand("smplOpenDialog", false, { title : '5 Columns', identifier : '5-col-20-20-20-20-20' })
									}
								},
							]
						},
		                {
		                	text: 'Post Grid',
		                	onclick: function() {
		                		//d.insertContent('Menu item 1');
		                		tinyMCE.activeEditor.execCommand("smplOpenDialog", false, { title : 'Post Grid', identifier : 'post_grid' })
		                	}
		                },
		                {
		                	text: 'Escape Auto Formatting',
		                	onclick: function() {
		                		//d.insertContent('Menu item 1');
		                		tinyMCE.activeEditor.execCommand("smplOpenDialog", false, { title : 'Escape Formatting', identifier : 'raw' })
		                	}
		                }
					]
				});
			}
		}
	);
	tinymce.PluginManager.add("SmplShortcodes",tinymce.plugins.SmplShortcodes)
})();