function base64ToArrayBuffer(b64) {
    var binary = atob(b64);
    var bytes = new Uint8Array(binary.length);
    for (var i = 0; i < binary.length; i++) {
        bytes[i] = binary.charCodeAt(i);
    }
    return bytes.buffer;
}

function buildPayload(form, searchMode) {
    if (searchMode === "1") {
        return { search: form.querySelector("#search").value };
    }

    var identifiers = {};
    form.querySelectorAll('[name^="identifiers["]').forEach(function (input) {
        var match = input.name.match(/identifiers\[(\d+)\]/);
        if (match) {
            identifiers[match[1]] = input.value;
        }
    });

    return { identifiers: identifiers };
}

async function encryptRequest(payload, publicKeyBase64) {
    var publicKeyBuffer = base64ToArrayBuffer(publicKeyBase64);

    var publicKey = await crypto.subtle.importKey(
        "spki",
        publicKeyBuffer,
        { name: "RSA-OAEP", hash: "SHA-1" },
        false,
        ["encrypt"],
    );

    var aesKey = await crypto.subtle.generateKey(
        { name: "AES-GCM", length: 256 },
        true,
        ["encrypt", "decrypt"],
    );

    var aesKeyRaw = await crypto.subtle.exportKey("raw", aesKey);

    var encryptedAesKey = await crypto.subtle.encrypt(
        { name: "RSA-OAEP" },
        publicKey,
        aesKeyRaw,
    );

    var iv = crypto.getRandomValues(new Uint8Array(12));

    var encoder = new TextEncoder();
    var payloadBytes = encoder.encode(JSON.stringify(payload));

    var encryptedPayload = await crypto.subtle.encrypt(
        { name: "AES-GCM", iv: iv },
        aesKey,
        payloadBytes,
    );

    var encAesKeyArr = new Uint8Array(encryptedAesKey);
    var encPayloadArr = new Uint8Array(encryptedPayload);

    var blob = new Uint8Array(
        encAesKeyArr.length + iv.length + encPayloadArr.length,
    );
    blob.set(encAesKeyArr, 0);
    blob.set(iv, encAesKeyArr.length);
    blob.set(encPayloadArr, encAesKeyArr.length + iv.length);

    return { blob: blob, aesKey: aesKey };
}

async function decryptResponse(responseBytes, aesKey) {
    var iv = responseBytes.slice(0, 12);
    var ciphertext = responseBytes.slice(12);

    var decrypted = await crypto.subtle.decrypt(
        { name: "AES-GCM", iv: iv },
        aesKey,
        ciphertext,
    );

    var decoder = new TextDecoder();

    return JSON.parse(decoder.decode(decrypted));
}

function renderResults(data, container) {
    container.innerHTML = "";

    if (data.found) {
        var resultsTmpl = document.getElementById("tmpl-results");
        var resultsClone = resultsTmpl.content.cloneNode(true);
        var fieldsContainer = resultsClone.querySelector("[data-fields]");

        data.fields.forEach(function (field) {
            var tmplId = "tmpl-field";

            if (field.type === 3) {
                tmplId = "tmpl-field-link";
            } else if (field.type === 4) {
                tmplId = "tmpl-field-attachment";
            }

            var fieldClone = document
                .getElementById(tmplId)
                .content.cloneNode(true);
            fieldClone.querySelector("[data-label]").textContent = field.label;

            var valueEl = fieldClone.querySelector("[data-value]");

            if (field.type === 5) {
                valueEl.textContent =
                    field.value === "1" || field.value === 1
                        ? field.true_label || "Yes"
                        : field.false_label || "No";
            } else if (field.type === 3 || field.type === 4) {
                valueEl.href = field.value;
                valueEl.textContent =
                    field.type === 4 ? "Download" : field.value;
            } else {
                valueEl.textContent = field.value;
            }

            fieldsContainer.appendChild(fieldClone);
        });

        container.appendChild(resultsClone);
    } else {
        var tmpl = document.getElementById("tmpl-not-found");
        var clone = tmpl.content.cloneNode(true);
        clone.querySelector("[data-message]").textContent = data.message;
        container.appendChild(clone);
    }
}

function renderError(data, container) {
    container.innerHTML = "";

    if (data.errors) {
        var messages = Object.values(data.errors).flat();
        messages.forEach(function (msg) {
            var clone = document
                .getElementById("tmpl-error")
                .content.cloneNode(true);
            clone.firstElementChild.textContent = msg;
            container.appendChild(clone);
        });
    } else if (data.lockout) {
        var clone = document
            .getElementById("tmpl-error")
            .content.cloneNode(true);
        clone.firstElementChild.textContent = data.lockout;
        container.appendChild(clone);
    } else if (data.message) {
        var clone = document
            .getElementById("tmpl-error")
            .content.cloneNode(true);
        clone.firstElementChild.textContent = data.message;
        container.appendChild(clone);
    }
}

document.addEventListener("DOMContentLoaded", function () {
    var form = document.getElementById("search-form");

    if (!form) {
        return;
    }

    var PUBLIC_KEY_BASE64 = form.dataset.publicKey;
    var SEARCH_URL = form.dataset.searchUrl;
    var SEARCH_MODE = form.dataset.searchMode;

    form.addEventListener("submit", async function (e) {
        e.preventDefault();

        var resultsContainer = document.getElementById("results-container");
        var errorContainer = document.getElementById("error-container");
        var submitBtn = document.getElementById("search-btn");
        var originalText = submitBtn.textContent;

        errorContainer.innerHTML = "";
        submitBtn.disabled = true;
        submitBtn.textContent = "Searching...";

        try {
            if (!window.crypto.subtle) {
                var clone = document
                    .getElementById("tmpl-error")
                    .content.cloneNode(true);
                clone.firstElementChild.textContent =
                    "Your browser does not support the required encryption features.";
                errorContainer.appendChild(clone);

                return;
            }

            var payload = buildPayload(e.target, SEARCH_MODE);
            var encrypted = await encryptRequest(payload, PUBLIC_KEY_BASE64);

            var response = await fetch(SEARCH_URL, {
                method: "POST",
                headers: {
                    "Content-Type": "application/octet-stream",
                    Accept: "application/octet-stream",
                },
                body: encrypted.blob,
            });

            var responseBuffer = await response.arrayBuffer();

            if (!response.ok) {
                if (responseBuffer.byteLength > 12) {
                    try {
                        var errorData = await decryptResponse(
                            new Uint8Array(responseBuffer),
                            encrypted.aesKey,
                        );
                        renderError(errorData, errorContainer);

                        return;
                    } catch (_) {}
                }

                var clone = document
                    .getElementById("tmpl-error")
                    .content.cloneNode(true);
                clone.firstElementChild.textContent =
                    "Request failed (status " +
                    response.status +
                    "). Please try again.";
                errorContainer.appendChild(clone);

                return;
            }

            var data = await decryptResponse(
                new Uint8Array(responseBuffer),
                encrypted.aesKey,
            );
            renderResults(data, resultsContainer);
        } catch (error) {
            var clone = document
                .getElementById("tmpl-error")
                .content.cloneNode(true);
            clone.firstElementChild.textContent =
                "An unexpected error occurred. Please try again.";
            errorContainer.appendChild(clone);
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    });
});
