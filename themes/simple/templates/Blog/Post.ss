<% with $Post %>
<!-- 
The sample data makes this redundant in a lot of cases so it looks bad

<h1>$Title ($Author)</h1>
--> 
$Content.markdown

$Tags
<% end_with %>

<a href="/posts">back</a>
