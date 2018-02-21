# osticket-sugarcrm

This osticket plug allows you to intergrate osticket into your sugarcrm

To install download and copy the files to the plugin directory of your osticket installation
Go into the admin panal of osticket and configure/enable the plugin<br>
Then go to Manage->Forms, and edit "Contact Information"<br>
Add A new field.  Label "Backend ID", Short Answer, Variable backend_id, then save<br>
Config the new field. Size 40, Max Length 30.  On Settings Tab uncheck all execpt Enabled, then save twice<br>
At this time there is a bug in ajax.users.php which prevents the displaying of search results (see https://github.com/osTicket/osTicket/pull/3456)<br>
You will need to either comment out the line as such<br>
// ->annotate(array('__relevance__' => new SqlCode(1)))<br>
or copy the included ajax.users.php over top of the one in include<br>
