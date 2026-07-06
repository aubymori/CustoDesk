(() => {

const richEditActions = {
    "bold": {
        markdown: {
            type: "simple",
            prefix: "**",
            postfix: "**",
        },
        bbcode: {
            type: "simple",
            prefix: "[b]",
            postfix: "[/b]",
        },
        shortcut: {
            ctrl: true,
            key: "b",
        }
    },
    "italic": {
        markdown: {
            type: "simple",
            prefix: "*",
            postfix: "*",
        },
        bbcode: {
            type: "simple",
            prefix: "[i]",
            postfix: "[/i]",
        },
        shortcut: {
            ctrl: true,
            key: "i",
        }
    },
    "underline": {
        markdown: {
            type: "simple",
            prefix: "__",
            postfix: "__",
        },
        bbcode: {
            type: "simple",
            prefix: "[u]",
            postfix: "[/u]",
        },
        shortcut: {
            ctrl: true,
            key: "u",
        }
    },
    "strikethrough": {
        markdown: {
            type: "simple",
            prefix: "~~",
            postfix: "~~",
        },
        bbcode: {
            type: "simple",
            prefix: "[s]",
            postfix: "[/s]",
        },
    },
    "color": {
        markdown: {
            type: "form",
            formId: "color",
            callback: function(content, params)
            {
                return {
                    selStart: `<span style="color:${params.color}">`.length,
                    selLength: content.length,
                    text: `<span style="color:${params.color}">${content}</span>`
                };
            },
        },
        bbcode: {
            type: "form",
            formId: "color",
            callback: function(content, params)
            {
                return {
                    selStart: `[color=${params.color}]`.length,
                    selLength: content.length,
                    text: `[color=${params.color}]${content}[/color]`
                };
            },
        }
    },

    "sub": {
        markdown: {
            type: "simple",
            prefix: "<sub>",
            postfix: "</sub>",
        },
        bbcode: {
            type: "simple",
            prefix: "[sub]",
            postfix: "[/sub]",
        },
        shortcut: {
            ctrl: true,
            key: "=",
        }
    },
    "sup": {
        markdown: {
            type: "simple",
            prefix: "<sup>",
            postfix: "</sup>",
        },
        bbcode: {
            type: "simple",
            prefix: "[sup]",
            postfix: "[/sup]",
        },
        shortcut: {
            ctrl: true,
            shift: true,
            key: "+",
        }
    },

    "h1": {
        markdown: {
            type: "simple",
            prefix: "<h1>",
            postfix: "</h1>",
        },
        bbcode: {
            type: "simple",
            prefix: "[h1]",
            postfix: "[/h1]",
        },
    },
    "h2": {
        markdown: {
            type: "simple",
            prefix: "<h2>",
            postfix: "</h2>",
        },
        bbcode: {
            type: "simple",
            prefix: "[h2]",
            postfix: "[/h2]",
        },
    },
    "h3": {
        markdown: {
            type: "simple",
            prefix: "<h3>",
            postfix: "</h3>",
        },
        bbcode: {
            type: "simple",
            prefix: "[h3]",
            postfix: "[/h3]",
        },
    },

    "quote": {
        markdown: {
            type: "regex",
            find: /^/gm,
            replace: "> ",
        },
        bbcode: {
            type: "simple",
            prefix: "[quote]",
            postfix: "[/quote]",
        }
    },
    "spoiler": {
        markdown: {
            type: "simple",
            prefix: ">!",
            postfix: "!<",
        },
        bbcode: {
            type: "simple",
            prefix: "[spoiler]",
            postfix: "[/spoiler]",
        }
    },
    "code": {
        markdown: {
            type: "simple",
            prefix: "`",
            postfix: "`",
        },
        bbcode: {
            type: "simple",
            prefix: "[code]",
            postfix: "[/code]",
        },
    },
    "pre": {
        markdown: {
            type: "simple",
            prefix: "```\n",
            postfix: "\n```",
        },
        bbcode: {
            type: "simple",
            prefix: "[pre]",
            postfix: "[/pre]",
        }
    },
    "link": {
        markdown: {
            type: "form",
            formId: "link",
            callback: function(content, params)
            {
                let url = params.url;
                let text = params.text ?? params.url;
                return `[${text}](${url})`;
            },
        },
        bbcode: {
            type: "form",
            formId: "link",
            callback: function(content, params)
            {
                let url = params.url;
                let text = params.text;
                if (text)
                    return `[url=${url}]${text}[/url]`;
                else
                    return `[url]${url}[/url]`;
            },
        }
    },
    "img": {
        markdown: {
            type: "form",
            formId: "img",
            callback: function(content, params)
            {
                let url = params.url;
                let alt = params.alt;
                return `![${alt}](${url})`;
            },
        },
        bbcode: {
            type: "form",
            formId: "img",
            callback: function(content, params)
            {
                let url = params.url;
                let alt = params.alt;
                if (alt)
                    return `[img=${alt}]${url}[/img]`;
                else
                    return `[img]${url}[/img]`;
            },
        }
    },

    "ul": {
        markdown: {
            type: "regex",
            find: /^/gm,
            replace: "- "
        },
        bbcode: {
            type: "regex",
            replacements: [
                {
                    find: /^/gm,
                    replace: "[*]"
                },
                {
                    find: /^/,
                    replace: "[list]\n"
                },
                {
                    find: /$/,
                    replace: "\n[/list]"
                },
            ]
        }
    },
    "ol": {
        markdown: {
            type: "regex",
            find: /^/gm,
            replace: "1. "
        },
        bbcode: {
            type: "regex",
            replacements: [
                {
                    find: /^/gm,
                    replace: "[*]"
                },
                {
                    find: /^/,
                    replace: "[list=1]\n"
                },
                {
                    find: /$/,
                    replace: "\n[/list]"
                },
            ]
        }
    },
};

let activeButton = null;

const markdownCheck = document.getElementById("richEditChoice-markdown");
const textArea = document.getElementById("richEditText");

function isMarkdown()
{
    return markdownCheck.checked;
}

function handleAction(name)
{
    const start = textArea.selectionStart;
    const end = textArea.selectionEnd;
    const dir = textArea.selectionDirection;

    let data = richEditActions[name][isMarkdown() ? "markdown" : "bbcode"];
    switch (data.type)
    {
        case "simple":
        {
            let replace =
                data.prefix +
                textArea.value.substring(start, end).trim() +
                data.postfix;
            textArea.focus();
            document.execCommand("insertText", false, replace);
            textArea.setSelectionRange(start + data.prefix.length, end + data.prefix.length, dir);
            break;
        }
        case "regex":
        {
            let replace;
            if (data.replacements)
            {
                replace = textArea.value.substring(start, end).trim();
                for (const replacement of data.replacements)
                {
                    replace = replace.replace(replacement.find, replacement.replace);
                }
            }
            else
            {
                replace = textArea.value.substring(start, end).trim().replace(data.find, data.replace);
            }

            textArea.focus();
            document.execCommand("insertText", false, replace);
            textArea.setSelectionRange(start, start + replace.length, dir);
            break;
        }
        case "form":
        {
            if (activeButton)
            {
                document.getElementById("richEditFormat-" + name).className = "";
                document.getElementById("richEditForm-" + data.formId).className = "richEditForm";
                
                let btn = activeButton;
                activeButton = null;
                if (btn == name)
                    break;
            }

            activeButton = name;
            document.getElementById("richEditFormat-" + name).className = "pushed";
            document.getElementById("richEditForm-" + data.formId).className = "richEditForm active";
            break;
        }
    }
}

for (const action in richEditActions)
{
    let btn = document.getElementById("richEditFormat-" + action);
    btn.addEventListener("click", (e) => {
        handleAction(action);
        e.preventDefault();
    });
}

document.addEventListener("click", function(e)
{
    if (e.target.className == "richEditForm-insert")
    {
        let form = document.querySelector(".richEditForm.active");
        let data = richEditActions[activeButton][isMarkdown() ? "markdown" : "bbcode"];
        let params = {};

        for (const paramElm of form.querySelectorAll("[data-name]"))
        {
            params[paramElm.getAttribute("data-name")] = paramElm.value;
            paramElm.value = "";
        }

        const start = textArea.selectionStart;
        const end = textArea.selectionEnd;
        const dir = textArea.selectionDirection;

        let replace = data.callback(textArea.value.substring(start, end).trim(), params);

        let replaceText, selStart, selEnd;
        if (typeof replace == "string")
        {
            replaceText = replace;
            selStart = start;
            selEnd = start + replace.length;
        }
        else
        {
            replaceText = replace.text;
            selStart = start + replace.selStart;
            selEnd = selStart + replace.selLength;
        }

        document.getElementById("richEditFormat-" + activeButton).className = "";
        form.className = "richEditForm";
        activeButton = null;

        textArea.focus();
        document.execCommand("insertText", false, replaceText);
        textArea.setSelectionRange(selStart, selEnd, dir);

        e.preventDefault();
    }
});

document.addEventListener("keydown", function(e)
{
    let form = document.querySelector(".richEditForm.active");
    if (form && form.contains(e.target) && e.key == "Enter")
    {
        let insert = form.querySelector(".richEditForm-insert");
        if (insert)
        {
            insert.click();
            e.preventDefault();
            return;
        }
    }

    if (document.activeElement == textArea)
    {
        for (const action in richEditActions)
        {
            let shortcut = richEditActions[action].shortcut;
            if (shortcut
            && e.key == shortcut.key
            && e.ctrlKey == !!shortcut.ctrl
            && e.shiftKey == !!shortcut.shift
            && e.altKey == !!shortcut.alt)
            {
                document.getElementById("richEditFormat-" + action).click();
                e.preventDefault();
            }
        }
    }
});

/* Specific code for specific buttons */

document.getElementById("richEditForm-colorPick").addEventListener("click", function(e)
{
    let picker = document.getElementById("richEditForm-pickedColor");
    let input = document.getElementById("richEditForm-colorColor");
    picker.value = input.value;
    picker.click();
    e.preventDefault();
});

document.getElementById("richEditForm-pickedColor").addEventListener("input", function(e)
{
    let picker = document.getElementById("richEditForm-pickedColor");
    let input = document.getElementById("richEditForm-colorColor");
    input.value = picker.value;
});

})();