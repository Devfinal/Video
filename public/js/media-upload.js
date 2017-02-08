/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};

/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {

/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;

/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};

/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);

/******/ 		// Flag the module as loaded
/******/ 		module.l = true;

/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}


/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;

/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;

/******/ 	// identity function for calling harmory imports with the correct context
/******/ 	__webpack_require__.i = function(value) { return value; };

/******/ 	// define getter function for harmory exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		Object.defineProperty(exports, name, {
/******/ 			configurable: false,
/******/ 			enumerable: true,
/******/ 			get: getter
/******/ 		});
/******/ 	};

/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};

/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };

/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";

/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ function(module, exports) {

eval("var $form = $('#save-post');\nvar $input = $('#media');\nvar $modal = $('#progress-modal');\nvar $progressbar = $modal.find('.progress');\n\n$form.on('submit', function (event) {\n    var containsVideo = false;\n    var fileList = $input.get(0).files;\n\n    for (var i = 0, l = fileList.length; i < l; i++) {\n        if (fileList.item(i).type.match('video')) {\n            containsVideo = true;\n\n            break;\n        }\n    }\n\n    if (containsVideo) {\n        var xhr = new XMLHttpRequest;\n\n        if (!(xhr && 'upload' in xhr && 'onprogress' in xhr.upload) || !window.FormData) {\n            return;\n        }\n\n        event.preventDefault();\n\n        xhr.upload.addEventListener('loadstart', function (event) {\n            $modal.modal({\n                backdrop: 'static',\n                keyboard: false\n            });\n        });\n\n        xhr.upload.addEventListener('progress', function (event) {\n            $progressbar.val((event.loaded / event.total) * 100);\n        });\n\n        xhr.upload.addEventListener('load', function (event) {\n            $modal.find('.modal-body').append(\n                '<p class=\"text-muted\">Processing video, this may take a while...</p>' +\n                '<div class=\"p-4\" style=\"position:relative\">' +\n                    '<div class=\"loader\">' +\n                        '<i class=\"fa fa-refresh fa-spin text-muted\"></i>' +\n                    '</div>' +\n                '</div>'\n            );\n        });\n\n        xhr.addEventListener('readystatechange', function (event) {\n            if (event.currentTarget.readyState === 4) {\n                if (event.currentTarget.status === 200 && window.location.href !== event.currentTarget.responseURL) {\n                    window.location = event.currentTarget.responseURL;\n                } else {\n                    document.open();\n                    document.write(event.currentTarget.responseText);\n                    document.close();\n                }\n            }\n        });\n\n        // make sure the tinyMCE content is dumped before creating the payload\n        $form.find('#content').html(tinymce.get('content').getContent());\n\n        xhr.open(event.currentTarget.getAttribute('method'), event.currentTarget.getAttribute('action'), true);\n        xhr.send(new FormData(event.currentTarget));\n    }\n});\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiMC5qcyIsInNvdXJjZXMiOlsid2VicGFjazovLy9yZXNvdXJjZXMvYXNzZXRzL2pzL21lZGlhLXVwbG9hZC5qcz8yYjA1Iiwid2VicGFjazovLy8/ZDQxZCJdLCJzb3VyY2VzQ29udGVudCI6WyJjb25zdCAkZm9ybSA9ICQoJyNzYXZlLXBvc3QnKTtcbmNvbnN0ICRpbnB1dCA9ICQoJyNtZWRpYScpO1xuY29uc3QgJG1vZGFsID0gJCgnI3Byb2dyZXNzLW1vZGFsJyk7XG5jb25zdCAkcHJvZ3Jlc3NiYXIgPSAkbW9kYWwuZmluZCgnLnByb2dyZXNzJyk7XG5cbiRmb3JtLm9uKCdzdWJtaXQnLCBldmVudCA9PiB7XG4gICAgdmFyIGNvbnRhaW5zVmlkZW8gPSBmYWxzZTtcbiAgICBjb25zdCBmaWxlTGlzdCA9ICRpbnB1dC5nZXQoMCkuZmlsZXM7XG5cbiAgICBmb3IgKGxldCBpID0gMCwgbCA9IGZpbGVMaXN0Lmxlbmd0aDsgaSA8IGw7IGkrKykge1xuICAgICAgICBpZiAoZmlsZUxpc3QuaXRlbShpKS50eXBlLm1hdGNoKCd2aWRlbycpKSB7XG4gICAgICAgICAgICBjb250YWluc1ZpZGVvID0gdHJ1ZTtcblxuICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICBpZiAoY29udGFpbnNWaWRlbykge1xuICAgICAgICBsZXQgeGhyID0gbmV3IFhNTEh0dHBSZXF1ZXN0O1xuXG4gICAgICAgIGlmICghKHhociAmJiAndXBsb2FkJyBpbiB4aHIgJiYgJ29ucHJvZ3Jlc3MnIGluIHhoci51cGxvYWQpIHx8ICF3aW5kb3cuRm9ybURhdGEpIHtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG5cbiAgICAgICAgeGhyLnVwbG9hZC5hZGRFdmVudExpc3RlbmVyKCdsb2Fkc3RhcnQnLCBldmVudCA9PiB7XG4gICAgICAgICAgICAkbW9kYWwubW9kYWwoe1xuICAgICAgICAgICAgICAgIGJhY2tkcm9wOiAnc3RhdGljJyxcbiAgICAgICAgICAgICAgICBrZXlib2FyZDogZmFsc2VcbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9KTtcblxuICAgICAgICB4aHIudXBsb2FkLmFkZEV2ZW50TGlzdGVuZXIoJ3Byb2dyZXNzJywgZXZlbnQgPT4ge1xuICAgICAgICAgICAgJHByb2dyZXNzYmFyLnZhbCgoZXZlbnQubG9hZGVkIC8gZXZlbnQudG90YWwpICogMTAwKTtcbiAgICAgICAgfSk7XG5cbiAgICAgICAgeGhyLnVwbG9hZC5hZGRFdmVudExpc3RlbmVyKCdsb2FkJywgZXZlbnQgPT4ge1xuICAgICAgICAgICAgJG1vZGFsLmZpbmQoJy5tb2RhbC1ib2R5JykuYXBwZW5kKFxuICAgICAgICAgICAgICAgICc8cCBjbGFzcz1cInRleHQtbXV0ZWRcIj5Qcm9jZXNzaW5nIHZpZGVvLCB0aGlzIG1heSB0YWtlIGEgd2hpbGUuLi48L3A+JyArXG4gICAgICAgICAgICAgICAgJzxkaXYgY2xhc3M9XCJwLTRcIiBzdHlsZT1cInBvc2l0aW9uOnJlbGF0aXZlXCI+JyArXG4gICAgICAgICAgICAgICAgICAgICc8ZGl2IGNsYXNzPVwibG9hZGVyXCI+JyArXG4gICAgICAgICAgICAgICAgICAgICAgICAnPGkgY2xhc3M9XCJmYSBmYS1yZWZyZXNoIGZhLXNwaW4gdGV4dC1tdXRlZFwiPjwvaT4nICtcbiAgICAgICAgICAgICAgICAgICAgJzwvZGl2PicgK1xuICAgICAgICAgICAgICAgICc8L2Rpdj4nXG4gICAgICAgICAgICApO1xuICAgICAgICB9KTtcblxuICAgICAgICB4aHIuYWRkRXZlbnRMaXN0ZW5lcigncmVhZHlzdGF0ZWNoYW5nZScsIGV2ZW50ID0+IHtcbiAgICAgICAgICAgIGlmIChldmVudC5jdXJyZW50VGFyZ2V0LnJlYWR5U3RhdGUgPT09IDQpIHtcbiAgICAgICAgICAgICAgICBpZiAoZXZlbnQuY3VycmVudFRhcmdldC5zdGF0dXMgPT09IDIwMCAmJiB3aW5kb3cubG9jYXRpb24uaHJlZiAhPT0gZXZlbnQuY3VycmVudFRhcmdldC5yZXNwb25zZVVSTCkge1xuICAgICAgICAgICAgICAgICAgICB3aW5kb3cubG9jYXRpb24gPSBldmVudC5jdXJyZW50VGFyZ2V0LnJlc3BvbnNlVVJMO1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgIGRvY3VtZW50Lm9wZW4oKTtcbiAgICAgICAgICAgICAgICAgICAgZG9jdW1lbnQud3JpdGUoZXZlbnQuY3VycmVudFRhcmdldC5yZXNwb25zZVRleHQpO1xuICAgICAgICAgICAgICAgICAgICBkb2N1bWVudC5jbG9zZSgpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG5cbiAgICAgICAgLy8gbWFrZSBzdXJlIHRoZSB0aW55TUNFIGNvbnRlbnQgaXMgZHVtcGVkIGJlZm9yZSBjcmVhdGluZyB0aGUgcGF5bG9hZFxuICAgICAgICAkZm9ybS5maW5kKCcjY29udGVudCcpLmh0bWwodGlueW1jZS5nZXQoJ2NvbnRlbnQnKS5nZXRDb250ZW50KCkpO1xuXG4gICAgICAgIHhoci5vcGVuKGV2ZW50LmN1cnJlbnRUYXJnZXQuZ2V0QXR0cmlidXRlKCdtZXRob2QnKSwgZXZlbnQuY3VycmVudFRhcmdldC5nZXRBdHRyaWJ1dGUoJ2FjdGlvbicpLCB0cnVlKTtcbiAgICAgICAgeGhyLnNlbmQobmV3IEZvcm1EYXRhKGV2ZW50LmN1cnJlbnRUYXJnZXQpKTtcbiAgICB9XG59KTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyByZXNvdXJjZXMvYXNzZXRzL2pzL21lZGlhLXVwbG9hZC5qcyIsInVuZGVmaW5lZFxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyAiXSwibWFwcGluZ3MiOiJBQUFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FDQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Iiwic291cmNlUm9vdCI6IiJ9");

/***/ }
/******/ ]);