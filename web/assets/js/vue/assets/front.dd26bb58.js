import{r as v,c as f,a,t as l,u,F as h,p as g,b as S,d as c,o as k,e as y}from"./vendor.caf1ffb9.js";const E="modulepreload",i={},b="/D:ProjectsZibingsProjectsZSFzsfwebassetsjs\vue/",P=function(o,n){return!n||n.length===0?o():Promise.all(n.map(e=>{if(e=`${b}${e}`,e in i)return;i[e]=!0;const s=e.endsWith(".css"),p=s?'[rel="stylesheet"]':"";if(document.querySelector(`link[href="${e}"]${p}`))return;const r=document.createElement("link");if(r.rel=s?"stylesheet":E,s||(r.as="script",r.crossOrigin=""),r.href=e,document.head.appendChild(r),s)return new Promise((_,m)=>{r.addEventListener("load",_),r.addEventListener("error",()=>m(new Error(`Unable to preload CSS for ${e}`)))})})).then(()=>o())};var V=(t,o)=>{const n=t.__vccOpts||t;for(const[e,s]of o)n[e]=s;return n};const d=t=>(g("data-v-d81cd978"),t=t(),S(),t),$=d(()=>a("p",null,[a("a",{href:"https://vitejs.dev/guide/features.html",target:"_blank"}," Vite Documentation "),c(" | "),a("a",{href:"https://v3.vuejs.org/",target:"_blank"},"Vue 3 Documentation")],-1)),j=d(()=>a("p",null,[c(" Edit "),a("code",null,"components/HelloWorld.vue"),c(" to test hot module replacement. ")],-1)),C={props:{msg:String},setup(t){const o=v({count:0});return(n,e)=>(k(),f(h,null,[a("h1",null,"Vue "+l(t.msg),1),$,a("button",{type:"button",onClick:e[0]||(e[0]=s=>u(o).count++)}," count is: "+l(u(o).count),1),j],64))}};var D=V(C,[["__scopeId","data-v-d81cd978"]]);P(()=>import("./modulepreload-polyfill.b7f2da20.js"),[]);const I={HelloWorld:D};for(const t of document.getElementsByClassName("vue-app"))y({template:t.innerHTML,components:I}).mount(t);
