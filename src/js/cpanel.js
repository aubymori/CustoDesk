document.querySelector("input[name=\"avatar\"]").addEventListener("input", function(e)
{
    let file = e.target.files[0];
    if (file)
    {
        let reader = new FileReader();
        reader.onload = function({ target })
        {
            document.querySelector("form .userIcon img").src = target.result;
        };
        reader.readAsDataURL(file);
    }
}, false);