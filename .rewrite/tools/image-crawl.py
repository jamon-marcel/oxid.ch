import re,urllib.request,urllib.parse,html,sys,collections
BASE="https://oxid-architektur.ch"
seeds=["/"]+["/"+l.strip() for l in open("/tmp/paths.txt") if l.strip()]
seen=set(); queue=list(seeds); imgs=collections.OrderedDict(); pages=0
while queue and pages<600:
    p=queue.pop(0)
    if p in seen: continue
    seen.add(p)
    try:
        r=urllib.request.urlopen(BASE+p,timeout=20); h=r.read().decode('utf-8','replace')
    except Exception as e:
        print("ERR",p,e,file=sys.stderr); continue
    pages+=1
    h=html.unescape(h)
    for u in re.findall(r'(?:https?://oxid-architektur\.ch)?(/img/(?:crop|home|large|thumbnail|original)/[^\s"\'\)]+)',h):
        imgs.setdefault(u,p)
    for a in re.findall(r'href="(?:https?://oxid-architektur\.ch)?(/[^"#?]*)"',h):
        if re.match(r'^/(img|css|js|fonts|storage|admin|api|media)/',a) or re.search(r'\.(pdf|jpg|png|svg|css|js|ico|xml)$',a,re.I): continue
        if a not in seen: queue.append(a)
open("/tmp/oxid-img/urls.txt","w").write("".join(f"{u} {p}\n" for u,p in imgs.items()))
print("pages",pages,"img urls",len(imgs))
