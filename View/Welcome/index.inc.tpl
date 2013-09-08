{if isset($user) }
    欢迎，{$user.display}。
{else}
    Welcome, whatever you are. 
    <a href="/Welcome/register">Register</a> 
{/if}