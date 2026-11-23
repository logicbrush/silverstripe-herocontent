<% if $VCard %><div class="vcard"><% end_if %>
	<% if $ContactName %>
	<$Tag class="fn">$ContactName.XML</$Tag>
	<% end_if %>
	<% if $OfficeAddr %>
	<$Tag class="adr">$OfficeAddr</$Tag>
	<% end_if %>
	<% if $OfficePhone %>
	<$Tag class="tel">
		<% if $VCard %><span class="type" title="work">P: </span><% end_if %>
		<span class="value">$OfficePhone.XML</span>
	</$Tag>
	<% end_if %>
	<% if $OfficeFax %>
	<$Tag class="tel">
		<% if $VCard %><span class="type" title="fax">F: </span><% end_if %>
		<span class="value">$OfficeFax.XML</span>
	</$Tag>
	<% end_if %>
	<% if $Email %>
	<$Tag><a href="mailto:{$Email.ATT}" class="email">$Email.XML</a></$Tag>
	<% end_if %>
<% if $VCard %></div><% end_if %>
