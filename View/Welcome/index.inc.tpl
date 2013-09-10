{if isset($user) }
    SSO: 欢迎，{$user.username}。
{else}
    SSO: Welcome, whatever you are. 
{/if}
    <a href="http://demo1.techotaku.net:8080/demo1">demo site 1</a> 
    <a href="http://demo2.techotaku.net:8080/demo2">demo site 2</a> 