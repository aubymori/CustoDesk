(() => {

let g_backgroundURI = document.getElementById("backgroundSection").getAttribute("data-bg-image");

function isDark(color)
{
    let r = parseInt(color.substring(1, 3), 16);
    let g = parseInt(color.substring(3, 5), 16);
    let b = parseInt(color.substring(5, 7), 16);
    let luminance = (r * 2 + g * 5 + b) / 8;
    return (luminance <= 128);
}

function buildStyle()
{
    let useCardColor = getInput("use_card_color").checked;
    let cardColor    = getInput("card_color").value;
    let useLinkColor = getInput("use_link_color").checked;
    let linkColor    = getInput("link_color").value;
    let useBg        = getInput("use_bg").checked;
    let bgColor      = getInput("bg_color").value;
    let bgFixed      = getInput("bg_fixed").checked;
    let bgAlignY     = getInput("bg_align_y").value;
    let bgAlignX     = getInput("bg_align_x").value;
    let bgRepeatY    = getInput("bg_repeat_y").checked;
    let bgRepeatX    = getInput("bg_repeat_x").checked;

    let bodyClass = [];
    if (useCardColor && !isDark(cardColor))
    {
        bodyClass.push("lightCards");
    }
    if (useBg && isDark(bgColor))
    {
        bodyClass.push("darkBg");
    }
    document.body.className = bodyClass.join(" ");

    let style = "";
    if (useCardColor)
    {
        style += `.cardHeader { background: ${cardColor} }`;
    }

    if (useLinkColor)
    {
        style += `a, a.blueHover:hover { color: ${linkColor} }`;
    }

    if (useBg)
    {
        let background = [ bgColor ];
        if (g_backgroundURI)
        {
            let bgRepeat = "";
            switch ((bgRepeatX ? 1 : 0) | (bgRepeatY ? 2 : 0))
            {
                case 0:
                    bgRepeat = "no-repeat";
                    break;
                case 1:
                    bgRepeat = "repeat-x";
                    break;
                case 2:
                    bgRepeat = "repeat-y";
                    break;
                case 4:
                    bgRepeat = "repeat";
                    break;
            }
            background.push(bgRepeat);

            background.push(`url("${g_backgroundURI}")`);

            if (bgFixed)
            {
                background.push("fixed");
            }

            background.push(bgAlignX);
            background.push(bgAlignY);
        }
        style += `body { background: ${background.join(" ")} }`;
    }

    document.getElementById("userTheme").innerHTML = style;
}

function getInput(name)
{
    return document.querySelector(`[name="${name}"]`);
}

function toggleSection(checkId, sectionId)
{
    let section = document.getElementById(sectionId);
    if (getInput(checkId).checked)
    {
        section.className = "";
        Array.from(section.querySelectorAll("input, button, select")).forEach(i => {
            i.disabled = false;
            i.removeAttribute("tabindex");
        });
    }
    else
    {
        section.className = "pending";
        Array.from(section.querySelectorAll("input, button, select")).forEach(i => {
            i.disabled = true;
            i.setAttribute("tabindex", "-1");
        });
    }
}

toggleSection("use_card_color", "cardColorSection");
toggleSection("use_link_color", "linkColorSection");
toggleSection("use_bg", "backgroundSection");

getInput("use_card_color").addEventListener("input", () => toggleSection("use_card_color", "cardColorSection"), false);
getInput("use_link_color").addEventListener("input", () => toggleSection("use_link_color", "linkColorSection"), false);
getInput("use_bg").addEventListener("input", () => toggleSection("use_bg", "backgroundSection"), false);

document.getElementById("profileEditor-pick").addEventListener("click", function(e)
{
    e.preventDefault();
    getInput("bg_image").click();
}, false);

document.getElementById("profileEditor-remove").addEventListener("click", function(e)
{
    g_backgroundURI = "";
    e.preventDefault();
    getInput("bg_image").value = "";
    getInput("remove_bg").value = "1";
    buildStyle();
}, false);

getInput("bg_image").addEventListener("input", function()
{
    let file = getInput("bg_image").files[0];
    if (file)
    {
        let reader = new FileReader();
        reader.onload = function({ target })
        {
            g_backgroundURI = target.result;
            buildStyle();
        };
        reader.readAsDataURL(file);
    }
}, false);

[
    "use_card_color",
    "card_color",
    "use_link_color",
    "link_color",
    "use_bg",
    "bg_color",
    "bg_fixed",
    "bg_align_y",
    "bg_align_x",
    "bg_repeat_x",
    "bg_repeat_y",
].map(name => getInput(name)).forEach(i => i.addEventListener("input", buildStyle, false));

})();