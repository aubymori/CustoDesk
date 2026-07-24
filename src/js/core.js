let custodesk = window.custodesk || {};

/* Cookie code */

custodesk.getCookie = function(name)
{
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(";").shift();
};

custodesk.setCookie = function(name, value)
{
    const date = new Date();
    date.setTime(date.getTime() + (400 * 24 * 60 * 60 * 1000)); // 400 days
    document.cookie = `${name}=${value};expires=${date.toUTCString()};path=/;SameSite=Strict`;
};

custodesk.deleteCookie = function(name)
{
    document.cookie = `${name}=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/;SameSite=Strict`
};

/* Menu code */

custodesk.toggleMenu = function(id, event)
{
    document.getElementById(id).classList.toggle("open");
    event.preventDefault();
    event.stopPropagation();
};

/* Follow/unfollow */

custodesk.followUser = function(id, link)
{
    custodesk.ajax("/ajax/follow_user", { id }, link, function()
    {
        link.parentNode.className += " following";
    });
};

custodesk.unfollowUser = function(id, link)
{
    custodesk.ajax("/ajax/unfollow_user", { id }, link, function()
    {
        link.parentNode.className
            = link.parentNode.className.replace(/ following$/, "");
    });
};

custodesk.ajax = function(endpoint, data, element, onSuccess, onFailure)
{
    let origOnclick = null;
    let origTabIndex = null;
    if (element)
    {
        element.className += " pending";

        origOnclick = element.onclick;
        element.onclick = null;

        origTabIndex = element.tabIndex;
        element.tabIndex = -1;
    }

    let req = new XMLHttpRequest;
    req.open("POST", endpoint);
    req.setRequestHeader("Content-Type", "application/json");
    req.onreadystatechange = function()
    {
        if (req.readyState == 4)
        {
            let json = JSON.parse(req.responseText);
            if (req.status == 200)
            {
                onSuccess(json);
            }
            else
            {
                if (json.alerts)
                {
                    let alertHTML = "";
                    for (const alert of json.alerts)
                    {
                        alertHTML += '<div class="alert alert' + alert.type + ' dismissible">';
                        alertHTML += '<div class="alertText">' + alert.text + '</div>';
                        alertHTML += '<button class="alertDismiss" title="Dismiss"></button>';
                        alertHTML += '</div>';
                    }
                    document.getElementById("alerts").innerHTML += alertHTML;
                }
                onFailure(json);
            }

            if (element)
            {
                element.className = element.className.replace(/ pending$/, "");

                element.onclick = origOnclick;
                element.tabIndex = origTabIndex;
            }
        }
    }
    req.send(JSON.stringify(data));
};

(() => {

/* Get and set timezone accordingly */
let tzOffset = (new Date).getTimezoneOffset();
if (tzOffset != custodesk.getCookie("tz"))
{
    custodesk.setCookie("tz", tzOffset);
    location.reload();
}

document.addEventListener("click", function(e)
{
    let menu = document.querySelector(".menu.open");
    if (menu)
    {
        menu.classList.remove("open");
    }

    if (e.target.className == "fileInputBtn")
    {
        let input = e.target.parentNode.querySelector("input");
        if (input)
        {
            input.click();
        }
        e.preventDefault();
    }
    else if (e.target.className == "alertDismiss")
    {
        e.target.parentNode.remove();
    }
    else if (e.target.className == "colorPickerPreview")
    {
        e.target.parentNode.parentNode.querySelector("input[type=\"color\"]").click();
        e.preventDefault();
    }
}, false);

document.addEventListener("input", function(e)
{
    let i = e.target;
    if (i.className.startsWith("colorPickerInput"))
    {
        if (i.value.match(/^#[0-9a-f]{6}$/i) === null)
        {
            if (i.className.indexOf(" bad") == -1)
                i.className += " bad";
        }
        else
        {
            i.className = i.className.replace(/ bad$/, "");
            i.parentNode.parentNode.querySelector(".colorPickerPreview").style.backgroundColor = i.value;

            let colorInput = i.parentNode.querySelector("input[type=\"color\"]");
            colorInput.value = i.value;
            colorInput.dispatchEvent(new InputEvent("input"));
        }
    }
    else if (i.type == "color" && i.parentNode.parentNode.parentNode.parentNode.className == "colorInput")
    {
        i.parentNode.querySelector(".colorPickerInput").value = i.value;
        i.parentNode.parentNode.querySelector(".colorPickerPreview").style.backgroundColor = i.value;
    }
    else if (i.type == "file" && i.parentNode.className == "fileInput")
    {
        let label = i.parentNode.querySelector(".fileInputLabel");
        if (label)
        {
            label.innerText = i.files[0].name;
        }
    }
}, false);

})();