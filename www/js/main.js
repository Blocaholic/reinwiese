const $ = id => document.getElementById(id);
const $$ = query => document.querySelectorAll(query);

const path = window.location.pathname.substring(1);
$(path)?.classList.add('header__navLi--active');
