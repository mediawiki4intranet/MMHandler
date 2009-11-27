if (!window.mmhandler_findpos)
{
    window.mmhandler_findpos = function(obj)
    {
        var x = 0, y = 0;
        if (obj.offsetParent)
        {
            do
            {
                x += obj.offsetLeft;
                y += obj.offsetTop;
            } while (obj = obj.offsetParent);
            return [x, y];
        }
    };
}

if (!window.mmhandler_unfold)
{
    window.mmhandler_unfold = function(i, f, sw, sh)
    {
        mm = document.getElementById('mindmap'+i);
        xy = mmhandler_findpos(mm);
        mm.style.height = f ? sh : window.innerHeight+'px';
        mm.style.width = f ? sw : (document.body.clientWidth-xy[0])+'px';
        window.scroll(0, xy[1]);
        mmf = document.getElementById('mindmapfold'+i);
        mmu = document.getElementById('mindmapunfold'+i);
        mmf.style.display = f ? 'none' : '';
        mmu.style.display = !f ? 'none' : '';
    };
}
