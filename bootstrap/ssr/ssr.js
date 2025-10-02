import { ref, watch, resolveComponent, mergeProps, unref, withCtx, createTextVNode, toDisplayString, createBlock, openBlock, Fragment, renderList, createVNode, useSSRContext, reactive, isRef, createCommentVNode, createSSRApp, h as h$1 } from "vue";
import { ssrRenderAttrs, ssrRenderStyle, ssrRenderComponent, ssrRenderList, ssrInterpolate, ssrRenderAttr, ssrRenderSlot } from "vue/server-renderer";
import Tabs from "primevue/tabs";
import TabList from "primevue/tablist";
import Tab from "primevue/tab";
import TabPanels from "primevue/tabpanels";
import TabPanel from "primevue/tabpanel";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import { router, usePage, createInertiaApp, Head, Link } from "@inertiajs/vue3";
import { Swiper, SwiperSlide } from "swiper/vue";
import { Autoplay, Pagination, EffectFade } from "swiper/modules";
import { Form, FormField } from "@primevue/forms";
import { z } from "zod";
import { zodResolver } from "@primevue/forms/resolvers/zod";
import Button from "primevue/button";
import DatePicker from "primevue/datepicker";
import Select from "primevue/select";
import createServer from "@inertiajs/vue3/server";
import { renderToString } from "@vue/server-renderer";
import PrimeVue from "primevue/config";
import Breadcrumb from "primevue/breadcrumb";
import Aura from "@primeuix/themes/aura";
import { svgSpritePlugin } from "vue-svg-sprite";
const _sfc_main$b = {
  __name: "Room",
  __ssrInlineRender: true,
  props: {
    roomList: {
      type: Array,
      required: false
    },
    roomTypeList: {
      type: Array,
      required: false
    },
    branchList: {
      type: Array,
      required: false
    },
    columns: {
      type: Array,
      required: true
    },
    activeTab: {
      type: String,
      default: "room_type"
    }
  },
  setup(__props) {
    let tabs = ["room_type", "room"];
    function capitalizeFirst(str) {
      return str.charAt(0).toUpperCase() + str.slice(1).replace(/_/g, " ");
    }
    const props = __props;
    const currentTab = ref(props.activeTab);
    watch(currentTab, (newTab) => {
      if (newTab === "room") {
        router.visit(route("room-management"));
      } else {
        router.visit(route("room-type-management"));
      }
    });
    return (_ctx, _push, _parent, _attrs) => {
      const _component_x_utility46multiple_select_tag = resolveComponent("x-utility.multiple-select-tag");
      _push(`<main${ssrRenderAttrs(mergeProps({ class: "room-type-section | padding-block-600" }, _attrs))}><div class="container"><div class="main-content"><section class="main-content__left"><div class="side-bar | flow" style="${ssrRenderStyle({ "--flow-spacer": "1em" })}"><div class="search-bar | box | flow" style="${ssrRenderStyle({ "--flow-spacer": "1em" })}"><label class="admin-label label" for="room-type-search">Tìm kiếm</label><input type="text" name="room-type-search" placeholder="Tìm kiếm hạng phòng"></div><div class="box">`);
      _push(ssrRenderComponent(_component_x_utility46multiple_select_tag, {
        id: "filter-branch-select",
        list: _ctx.$branchList,
        placeholder: "Chọn chi nhánh"
      }, null, _parent));
      _push(`</div><form class="filter-status | box | flow" style="${ssrRenderStyle({ "--flow-spacer": "1em" })}"><label class="label admin-label" for="room-status">Trạng thái</label><div class="radio-select | form-check"><input class="form-check-input" type="radio" name="filter-status" value="active" id="active-room"><label class="form-check-label" for="filter-status"> Đang kinh doanh </label></div><div class="radio-select | form-check"><input class="form-check-input" type="radio" name="filter-status" value="inactive" id="inactive-room"><label class="form-check-label" for="filter-status"> Ngừng kinh doanh </label></div><div class="radio-select | form-check"><input class="form-check-input" type="radio" name="filter-status" value="all" id="all-room" checked><label class="form-check-label" for="filter-status"> Tất cả </label></div></form><section class="record-quantity"><label for="record">Số bản ghi: </label><select name="record" id="record" class="nice-select"><option value="15">15</option><option value="20">20</option><option value="30">30</option><option value="50">50</option></select></section></div></section><section class="main-content__right | flow" style="${ssrRenderStyle({ "--flow-spacer": "1em" })}"><nav class="table-toolbar"><div class="nav-wrapper"><div class="filter-search"><span class="label admin-label | fs-normal-heading">Hạng phòng &amp; Phòng</span></div></div></nav>`);
      _push(ssrRenderComponent(unref(Tabs), {
        modelValue: currentTab.value,
        "onUpdate:modelValue": ($event) => currentTab.value = $event
      }, {
        default: withCtx((_2, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(unref(TabList), null, {
              default: withCtx((_3, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<!--[-->`);
                  ssrRenderList(unref(tabs), (tab) => {
                    _push3(ssrRenderComponent(unref(Tab), {
                      key: tab,
                      value: tab
                    }, {
                      default: withCtx((_4, _push4, _parent4, _scopeId3) => {
                        if (_push4) {
                          _push4(`${ssrInterpolate(capitalizeFirst(tab))}`);
                        } else {
                          return [
                            createTextVNode(toDisplayString(capitalizeFirst(tab)), 1)
                          ];
                        }
                      }),
                      _: 2
                    }, _parent3, _scopeId2));
                  });
                  _push3(`<!--]-->`);
                } else {
                  return [
                    (openBlock(true), createBlock(Fragment, null, renderList(unref(tabs), (tab) => {
                      return openBlock(), createBlock(unref(Tab), {
                        key: tab,
                        value: tab
                      }, {
                        default: withCtx(() => [
                          createTextVNode(toDisplayString(capitalizeFirst(tab)), 1)
                        ]),
                        _: 2
                      }, 1032, ["value"]);
                    }), 128))
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(unref(TabPanels), null, {
              default: withCtx((_3, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(ssrRenderComponent(unref(TabPanel), { value: "room" }, {
                    default: withCtx((_4, _push4, _parent4, _scopeId3) => {
                      if (_push4) {
                        _push4(ssrRenderComponent(unref(DataTable), {
                          value: __props.roomList,
                          tableStyle: "min-width: 50rem"
                        }, {
                          default: withCtx((_5, _push5, _parent5, _scopeId4) => {
                            if (_push5) {
                              _push5(`<!--[-->`);
                              ssrRenderList(__props.columns, (col) => {
                                _push5(ssrRenderComponent(unref(Column), {
                                  key: col,
                                  field: col,
                                  header: capitalizeFirst(col)
                                }, null, _parent5, _scopeId4));
                              });
                              _push5(`<!--]-->`);
                            } else {
                              return [
                                (openBlock(true), createBlock(Fragment, null, renderList(__props.columns, (col) => {
                                  return openBlock(), createBlock(unref(Column), {
                                    key: col,
                                    field: col,
                                    header: capitalizeFirst(col)
                                  }, null, 8, ["field", "header"]);
                                }), 128))
                              ];
                            }
                          }),
                          _: 1
                        }, _parent4, _scopeId3));
                      } else {
                        return [
                          createVNode(unref(DataTable), {
                            value: __props.roomList,
                            tableStyle: "min-width: 50rem"
                          }, {
                            default: withCtx(() => [
                              (openBlock(true), createBlock(Fragment, null, renderList(__props.columns, (col) => {
                                return openBlock(), createBlock(unref(Column), {
                                  key: col,
                                  field: col,
                                  header: capitalizeFirst(col)
                                }, null, 8, ["field", "header"]);
                              }), 128))
                            ]),
                            _: 1
                          }, 8, ["value"])
                        ];
                      }
                    }),
                    _: 1
                  }, _parent3, _scopeId2));
                  _push3(ssrRenderComponent(unref(TabPanel), { value: "room_type" }, {
                    default: withCtx((_4, _push4, _parent4, _scopeId3) => {
                      if (_push4) {
                        _push4(ssrRenderComponent(unref(DataTable), {
                          value: __props.roomTypeList,
                          tableStyle: "min-width: 50rem"
                        }, {
                          default: withCtx((_5, _push5, _parent5, _scopeId4) => {
                            if (_push5) {
                              _push5(`<!--[-->`);
                              ssrRenderList(__props.columns, (col) => {
                                _push5(ssrRenderComponent(unref(Column), {
                                  key: col,
                                  field: col,
                                  header: capitalizeFirst(col)
                                }, null, _parent5, _scopeId4));
                              });
                              _push5(`<!--]-->`);
                            } else {
                              return [
                                (openBlock(true), createBlock(Fragment, null, renderList(__props.columns, (col) => {
                                  return openBlock(), createBlock(unref(Column), {
                                    key: col,
                                    field: col,
                                    header: capitalizeFirst(col)
                                  }, null, 8, ["field", "header"]);
                                }), 128))
                              ];
                            }
                          }),
                          _: 1
                        }, _parent4, _scopeId3));
                      } else {
                        return [
                          createVNode(unref(DataTable), {
                            value: __props.roomTypeList,
                            tableStyle: "min-width: 50rem"
                          }, {
                            default: withCtx(() => [
                              (openBlock(true), createBlock(Fragment, null, renderList(__props.columns, (col) => {
                                return openBlock(), createBlock(unref(Column), {
                                  key: col,
                                  field: col,
                                  header: capitalizeFirst(col)
                                }, null, 8, ["field", "header"]);
                              }), 128))
                            ]),
                            _: 1
                          }, 8, ["value"])
                        ];
                      }
                    }),
                    _: 1
                  }, _parent3, _scopeId2));
                } else {
                  return [
                    createVNode(unref(TabPanel), { value: "room" }, {
                      default: withCtx(() => [
                        createVNode(unref(DataTable), {
                          value: __props.roomList,
                          tableStyle: "min-width: 50rem"
                        }, {
                          default: withCtx(() => [
                            (openBlock(true), createBlock(Fragment, null, renderList(__props.columns, (col) => {
                              return openBlock(), createBlock(unref(Column), {
                                key: col,
                                field: col,
                                header: capitalizeFirst(col)
                              }, null, 8, ["field", "header"]);
                            }), 128))
                          ]),
                          _: 1
                        }, 8, ["value"])
                      ]),
                      _: 1
                    }),
                    createVNode(unref(TabPanel), { value: "room_type" }, {
                      default: withCtx(() => [
                        createVNode(unref(DataTable), {
                          value: __props.roomTypeList,
                          tableStyle: "min-width: 50rem"
                        }, {
                          default: withCtx(() => [
                            (openBlock(true), createBlock(Fragment, null, renderList(__props.columns, (col) => {
                              return openBlock(), createBlock(unref(Column), {
                                key: col,
                                field: col,
                                header: capitalizeFirst(col)
                              }, null, 8, ["field", "header"]);
                            }), 128))
                          ]),
                          _: 1
                        }, 8, ["value"])
                      ]),
                      _: 1
                    })
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(unref(TabList), null, {
                default: withCtx(() => [
                  (openBlock(true), createBlock(Fragment, null, renderList(unref(tabs), (tab) => {
                    return openBlock(), createBlock(unref(Tab), {
                      key: tab,
                      value: tab
                    }, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(capitalizeFirst(tab)), 1)
                      ]),
                      _: 2
                    }, 1032, ["value"]);
                  }), 128))
                ]),
                _: 1
              }),
              createVNode(unref(TabPanels), null, {
                default: withCtx(() => [
                  createVNode(unref(TabPanel), { value: "room" }, {
                    default: withCtx(() => [
                      createVNode(unref(DataTable), {
                        value: __props.roomList,
                        tableStyle: "min-width: 50rem"
                      }, {
                        default: withCtx(() => [
                          (openBlock(true), createBlock(Fragment, null, renderList(__props.columns, (col) => {
                            return openBlock(), createBlock(unref(Column), {
                              key: col,
                              field: col,
                              header: capitalizeFirst(col)
                            }, null, 8, ["field", "header"]);
                          }), 128))
                        ]),
                        _: 1
                      }, 8, ["value"])
                    ]),
                    _: 1
                  }),
                  createVNode(unref(TabPanel), { value: "room_type" }, {
                    default: withCtx(() => [
                      createVNode(unref(DataTable), {
                        value: __props.roomTypeList,
                        tableStyle: "min-width: 50rem"
                      }, {
                        default: withCtx(() => [
                          (openBlock(true), createBlock(Fragment, null, renderList(__props.columns, (col) => {
                            return openBlock(), createBlock(unref(Column), {
                              key: col,
                              field: col,
                              header: capitalizeFirst(col)
                            }, null, 8, ["field", "header"]);
                          }), 128))
                        ]),
                        _: 1
                      }, 8, ["value"])
                    ]),
                    _: 1
                  })
                ]),
                _: 1
              })
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</section></div></div></main>`);
    };
  }
};
const _sfc_setup$b = _sfc_main$b.setup;
_sfc_main$b.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/Room.vue");
  return _sfc_setup$b ? _sfc_setup$b(props, ctx) : void 0;
};
const __vite_glob_0_0 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: _sfc_main$b
}, Symbol.toStringTag, { value: "Module" }));
const _sfc_main$a = {
  __name: "About",
  __ssrInlineRender: true,
  setup(__props) {
    const page = usePage();
    const breadcrumb = page.props.breadcrumb;
    const pageTitle = ` | ${page.component.replace(/^(Guest\/|Admin\/)/, "")} Page`;
    let mainServiceList = [
      {
        name: "Restaurants",
        imgUrl: "/img/about/about-p1.jpg"
      },
      {
        name: "Travel & Camping",
        imgUrl: "/img/about/about-p2.jpg"
      },
      {
        name: "Event & Party",
        imgUrl: "/img/about/about-p3.jpg"
      }
    ];
    let galleryImageList = ["/img/gallery/gallery-1.jpg", "/img/gallery/gallery-4.jpg", "/img/gallery/gallery-3.jpg", "/img/gallery/gallery-2.jpg"];
    return (_ctx, _push, _parent, _attrs) => {
      const _component_Head = resolveComponent("Head");
      const _component_Breadcrumb = resolveComponent("Breadcrumb");
      const _component_SvgSprite = resolveComponent("SvgSprite");
      _push(`<!--[-->`);
      _push(ssrRenderComponent(_component_Head, { title: pageTitle }, null, _parent));
      _push(ssrRenderComponent(_component_Breadcrumb, {
        model: unref(breadcrumb),
        pt: { root: { class: "justify-center" }, item: { class: "text-xl" } }
      }, {
        item: withCtx(({ item, props }, _push2, _parent2, _scopeId) => {
          if (_push2) {
            if (item === unref(breadcrumb)[unref(breadcrumb).length - 1]) {
              _push2(`<a class="font-medium text-orange-300"${_scopeId}>${ssrInterpolate(item.label)}</a>`);
            } else {
              _push2(`<a${ssrRenderAttrs(props.action)}${_scopeId}>${ssrInterpolate(item.label)}</a>`);
            }
          } else {
            return [
              item === unref(breadcrumb)[unref(breadcrumb).length - 1] ? (openBlock(), createBlock("a", {
                key: 0,
                class: "font-medium text-orange-300"
              }, toDisplayString(item.label), 1)) : (openBlock(), createBlock("a", mergeProps({ key: 1 }, props.action), toDisplayString(item.label), 17))
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<div class="about-us-page-section | padding-block-600"><div class="container"><div class="about-us__text | even-columns padding-block-400" style="${ssrRenderStyle({ "--custom-gap": "3rem" })}"><div class="about-us__title | flow text-center flex flex-col justify-center align-center" style="${ssrRenderStyle({ "--flow-spacer": "1em" })}"><h2 class="fs-secondary-heading">Welcome to Sona.</h2><p>Built in 1910 during the Belle Epoque period, this hotel is located in the center of Paris, with easy access to the city’s tourist attractions. It offers tastefully decorated rooms. </p></div><ul class="about-us__services-list | flow pl-8" style="${ssrRenderStyle({ "--flow-spacer": ".75em" })}"><li class="grid grid-cols-[20px_1fr] items-center gap-2">`);
      _push(ssrRenderComponent(_component_SvgSprite, {
        symbol: "check",
        role: "presentation",
        class: "icon service-icon",
        "data-size": "small"
      }, null, _parent));
      _push(`<span class="fs-700">20% Off On Accommodation.</span></li><li class="grid grid-cols-[20px_1fr] items-center gap-2">`);
      _push(ssrRenderComponent(_component_SvgSprite, {
        symbol: "check",
        role: "presentation",
        class: "icon service-icon",
        "data-size": "small"
      }, null, _parent));
      _push(`<span class="fs-700">Complimentary Daily Breakfast</span></li><li class="grid grid-cols-[20px_1fr] items-center gap-2">`);
      _push(ssrRenderComponent(_component_SvgSprite, {
        symbol: "check",
        role: "presentation",
        class: "icon service-icon",
        "data-size": "small"
      }, null, _parent));
      _push(`<span class="fs-700">3 Pcs Laundry Per Day</span></li><li class="grid grid-cols-[20px_1fr] items-center gap-2">`);
      _push(ssrRenderComponent(_component_SvgSprite, {
        symbol: "check",
        role: "presentation",
        class: "icon service-icon",
        "data-size": "small"
      }, null, _parent));
      _push(`<span class="fs-700">Free Wifi.</span></li><li class="grid grid-cols-[20px_1fr] items-center gap-2">`);
      _push(ssrRenderComponent(_component_SvgSprite, {
        symbol: "check",
        role: "presentation",
        class: "icon service-icon",
        "data-size": "small"
      }, null, _parent));
      _push(`<span class="fs-700">Discount 20% On F&amp;B</span></li></ul></div><div class="about-us__services | padding-block-600 grid grid-cols-1 lg:grid-cols-3 gap-4"><!--[-->`);
      ssrRenderList(unref(mainServiceList), (item) => {
        _push(`<div class="about-us__services-item | set-bg-img" style="${ssrRenderStyle(_ctx.$getBgStyle(item.imgUrl))}"><span class="about-us__services-item-title">${ssrInterpolate(item.name)}</span></div>`);
      });
      _push(`<!--]--></div></div></div><div class="gallery-section | padding-block-400"><div class="container"><div class="gallery__text | text-center padding-block-400 flow" style="${ssrRenderStyle({ "--flow-spacer": ".5em" })}"><span class="label">Our Gallery</span><h2 class="fs-secondary-heading">Discover Our Work</h2></div><div class="gallery__list | grid grid-cols-1 lg:grid-cols-2 gap-4 items-stretch padding-block-600"><div class="grid grid-rows-2 gap-3">`);
      if (unref(galleryImageList)[0]) {
        _push(`<div class="gallery-item | set-bg-img" style="${ssrRenderStyle(_ctx.$getBgStyle(unref(galleryImageList)[0]))}"><div class="gallery-item__content | align-center justify-center"><span class="gallery-item__title">Room luxury</span></div></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`<div class="grid grid-cols-2 gap-2"><!--[-->`);
      ssrRenderList(unref(galleryImageList).slice(1, 3), (img) => {
        _push(`<div class="gallery-item | set-bg-img" style="${ssrRenderStyle(_ctx.$getBgStyle(img))}"><div class="gallery-item__content | align-center justify-center"><span class="gallery-item__title">Room luxury </span></div></div>`);
      });
      _push(`<!--]--></div></div>`);
      if (unref(galleryImageList)[3]) {
        _push(`<div class="gallery-item | set-bg-img aspect-[6/5]" style="${ssrRenderStyle(_ctx.$getBgStyle(unref(galleryImageList)[3]))}"><div class="gallery-item__content | align-center justify-center"><span class="gallery-item__title">Room luxury</span></div></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div></div></div><!--]-->`);
    };
  }
};
const _sfc_setup$a = _sfc_main$a.setup;
_sfc_main$a.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Guest/About.vue");
  return _sfc_setup$a ? _sfc_setup$a(props, ctx) : void 0;
};
const __vite_glob_0_1 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: _sfc_main$a
}, Symbol.toStringTag, { value: "Module" }));
const _sfc_main$9 = {
  __name: "Hero-section",
  __ssrInlineRender: true,
  setup(__props) {
    let checkBooking = reactive({
      checkIn: "",
      checkOut: "",
      guests: 1,
      rooms: 1
    });
    let guestOptions = ["1 Adult", "2 Adults", "3 Adults"];
    let roomOptions = ["1 Room", "2 Rooms", "3 Rooms"];
    const schema = z.object({
      checkIn: z.date({ required_error: "Vui lòng chọn ngày check-in" }),
      checkOut: z.date({ required_error: "Vui lòng chọn ngày check-out" }),
      guests: z.string().nonempty("Vui lòng chọn số lượng khách"),
      rooms: z.string().nonempty("Vui lòng chọn số lượng phòng")
    }).refine((data) => data.checkOut > data.checkIn, {
      message: "Ngày check-out phải sau check-in",
      path: ["checkOut"]
    });
    const onSubmit = (values) => {
      console.log("Form data:", values);
      alert("Kiểm tra phòng thành công ✅");
    };
    let imagesList = ref(["/img/hero/hero-1.jpg", "/img/hero/hero-2.jpg", "/img/hero/hero-3.jpg"]);
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<section${ssrRenderAttrs(mergeProps({ class: "hero | align-center justify-center padding-block-400 | mb-8" }, _attrs))}><div class="hero__content"><div class="container"><div class="even-columns" style="${ssrRenderStyle({ "--custom-gap": "var(--size-800)" })}"><div class="align-center"><div class="hero__text | flow"><h1 class="fs-primary-heading">Sona A luxury hotel</h1><p data-width="wide">Here are the best hotel booking sites, including recommendations for international travel and for finding low-priced hotel rooms.</p><button class="button">Discover now</button></div></div><div class="align-center justify-center"><div class="booking-form | padding-block-600 flow"><h5 class="fs-normal-heading | text-center">Booking your hotel</h5>`);
      _push(ssrRenderComponent(unref(Form), {
        modelValue: unref(checkBooking),
        "onUpdate:modelValue": ($event) => isRef(checkBooking) ? checkBooking.value = $event : checkBooking = $event,
        resolver: unref(zodResolver)(unref(schema)),
        onSubmit,
        class: "space-y-4"
      }, {
        default: withCtx((_2, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div${_scopeId}><label class="block mb-1"${_scopeId}>Check-in</label>`);
            _push2(ssrRenderComponent(unref(FormField), { name: "checkIn" }, {
              default: withCtx(({ field, error }, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(ssrRenderComponent(unref(DatePicker), mergeProps(field, {
                    dateFormat: "dd/mm/yy",
                    showIcon: "",
                    class: "w-full"
                  }), null, _parent3, _scopeId2));
                  if (error) {
                    _push3(`<small class="text-red-500"${_scopeId2}>${ssrInterpolate(error.message)}</small>`);
                  } else {
                    _push3(`<!---->`);
                  }
                } else {
                  return [
                    createVNode(unref(DatePicker), mergeProps(field, {
                      dateFormat: "dd/mm/yy",
                      showIcon: "",
                      class: "w-full"
                    }), null, 16),
                    error ? (openBlock(), createBlock("small", {
                      key: 0,
                      class: "text-red-500"
                    }, toDisplayString(error.message), 1)) : createCommentVNode("", true)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</div><div${_scopeId}><label class="block mb-1"${_scopeId}>Check-out</label>`);
            _push2(ssrRenderComponent(unref(FormField), { name: "checkOut" }, {
              default: withCtx(({ field, error }, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(ssrRenderComponent(unref(DatePicker), mergeProps(field, {
                    dateFormat: "dd/mm/yy",
                    showIcon: "",
                    class: "w-full"
                  }), null, _parent3, _scopeId2));
                  if (error) {
                    _push3(`<small class="text-red-500"${_scopeId2}>${ssrInterpolate(error.message)}</small>`);
                  } else {
                    _push3(`<!---->`);
                  }
                } else {
                  return [
                    createVNode(unref(DatePicker), mergeProps(field, {
                      dateFormat: "dd/mm/yy",
                      showIcon: "",
                      class: "w-full"
                    }), null, 16),
                    error ? (openBlock(), createBlock("small", {
                      key: 0,
                      class: "text-red-500"
                    }, toDisplayString(error.message), 1)) : createCommentVNode("", true)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</div><div${_scopeId}><label class="block mb-1"${_scopeId}>Guest</label>`);
            _push2(ssrRenderComponent(unref(FormField), { name: "guests" }, {
              default: withCtx(({ field, error }, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(ssrRenderComponent(unref(Select), mergeProps(field, {
                    options: unref(guestOptions),
                    class: "w-full"
                  }), null, _parent3, _scopeId2));
                  if (error) {
                    _push3(`<small class="text-red-500"${_scopeId2}>${ssrInterpolate(error.message)}</small>`);
                  } else {
                    _push3(`<!---->`);
                  }
                } else {
                  return [
                    createVNode(unref(Select), mergeProps(field, {
                      options: unref(guestOptions),
                      class: "w-full"
                    }), null, 16, ["options"]),
                    error ? (openBlock(), createBlock("small", {
                      key: 0,
                      class: "text-red-500"
                    }, toDisplayString(error.message), 1)) : createCommentVNode("", true)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</div><div${_scopeId}><label class="block mb-1"${_scopeId}>Room</label>`);
            _push2(ssrRenderComponent(unref(FormField), { name: "rooms" }, {
              default: withCtx(({ field, error }, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(ssrRenderComponent(unref(Select), mergeProps(field, {
                    options: unref(roomOptions),
                    class: "w-full"
                  }), null, _parent3, _scopeId2));
                  if (error) {
                    _push3(`<small class="text-red-500"${_scopeId2}>${ssrInterpolate(error.message)}</small>`);
                  } else {
                    _push3(`<!---->`);
                  }
                } else {
                  return [
                    createVNode(unref(Select), mergeProps(field, {
                      options: unref(roomOptions),
                      class: "w-full"
                    }), null, 16, ["options"]),
                    error ? (openBlock(), createBlock("small", {
                      key: 0,
                      class: "text-red-500"
                    }, toDisplayString(error.message), 1)) : createCommentVNode("", true)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</div>`);
            _push2(ssrRenderComponent(unref(Button), {
              type: "submit",
              label: "Check availability",
              class: "w-full mt-4"
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode("div", null, [
                createVNode("label", { class: "block mb-1" }, "Check-in"),
                createVNode(unref(FormField), { name: "checkIn" }, {
                  default: withCtx(({ field, error }) => [
                    createVNode(unref(DatePicker), mergeProps(field, {
                      dateFormat: "dd/mm/yy",
                      showIcon: "",
                      class: "w-full"
                    }), null, 16),
                    error ? (openBlock(), createBlock("small", {
                      key: 0,
                      class: "text-red-500"
                    }, toDisplayString(error.message), 1)) : createCommentVNode("", true)
                  ]),
                  _: 1
                })
              ]),
              createVNode("div", null, [
                createVNode("label", { class: "block mb-1" }, "Check-out"),
                createVNode(unref(FormField), { name: "checkOut" }, {
                  default: withCtx(({ field, error }) => [
                    createVNode(unref(DatePicker), mergeProps(field, {
                      dateFormat: "dd/mm/yy",
                      showIcon: "",
                      class: "w-full"
                    }), null, 16),
                    error ? (openBlock(), createBlock("small", {
                      key: 0,
                      class: "text-red-500"
                    }, toDisplayString(error.message), 1)) : createCommentVNode("", true)
                  ]),
                  _: 1
                })
              ]),
              createVNode("div", null, [
                createVNode("label", { class: "block mb-1" }, "Guest"),
                createVNode(unref(FormField), { name: "guests" }, {
                  default: withCtx(({ field, error }) => [
                    createVNode(unref(Select), mergeProps(field, {
                      options: unref(guestOptions),
                      class: "w-full"
                    }), null, 16, ["options"]),
                    error ? (openBlock(), createBlock("small", {
                      key: 0,
                      class: "text-red-500"
                    }, toDisplayString(error.message), 1)) : createCommentVNode("", true)
                  ]),
                  _: 1
                })
              ]),
              createVNode("div", null, [
                createVNode("label", { class: "block mb-1" }, "Room"),
                createVNode(unref(FormField), { name: "rooms" }, {
                  default: withCtx(({ field, error }) => [
                    createVNode(unref(Select), mergeProps(field, {
                      options: unref(roomOptions),
                      class: "w-full"
                    }), null, 16, ["options"]),
                    error ? (openBlock(), createBlock("small", {
                      key: 0,
                      class: "text-red-500"
                    }, toDisplayString(error.message), 1)) : createCommentVNode("", true)
                  ]),
                  _: 1
                })
              ]),
              createVNode(unref(Button), {
                type: "submit",
                label: "Check availability",
                class: "w-full mt-4"
              })
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div></div></div></div></div>`);
      _push(ssrRenderComponent(unref(Swiper), {
        modules: [unref(Autoplay), unref(Pagination), unref(EffectFade)],
        spaceBetween: 30,
        centeredSlides: true,
        "slides-per-view": 1,
        autoplay: {
          delay: 5e3,
          disableOnInteraction: false
        },
        pagination: { clickable: true },
        effect: "fade",
        class: "hero__slider"
      }, {
        default: withCtx((_2, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<!--[-->`);
            ssrRenderList(unref(imagesList), (image, i2) => {
              _push2(ssrRenderComponent(unref(SwiperSlide), {
                class: "slider-item set-bg-img",
                key: i2,
                style: _ctx.$getBgStyle(image)
              }, null, _parent2, _scopeId));
            });
            _push2(`<!--]-->`);
          } else {
            return [
              (openBlock(true), createBlock(Fragment, null, renderList(unref(imagesList), (image, i2) => {
                return openBlock(), createBlock(unref(SwiperSlide), {
                  class: "slider-item set-bg-img",
                  key: i2,
                  style: _ctx.$getBgStyle(image)
                }, null, 8, ["style"]);
              }), 128))
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</section>`);
    };
  }
};
const _sfc_setup$9 = _sfc_main$9.setup;
_sfc_main$9.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Guest/Hero-section.vue");
  return _sfc_setup$9 ? _sfc_setup$9(props, ctx) : void 0;
};
const _sfc_main$8 = {
  __name: "About-section",
  __ssrInlineRender: true,
  setup(__props) {
    let imagesList = ref(["/img/about/about-1.jpg", "/img/about/about-2.jpg"]);
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<section${ssrRenderAttrs(mergeProps({ class: "about-section | padding-block-1000 section-divider" }, _attrs))}><div class="container"><div class="even-columns" style="${ssrRenderStyle({ "--custom-gap": "2rem" })}"><div class="align-center justify-center"><div class="about__content | text-center flow" style="${ssrRenderStyle({ "--flow-spacer": "1rem" })}"><span class="label">About us</span><h3 class="fs-secondary-heading"> Intercontinental LA <br> Westlake Hotel </h3><p> Sona.com is a leading online accommodation site. We’re passionate about travel. Every day, we inspire and reach millions of travelers across 90 local websites in 41 languages. </p><p> So when it comes to booking the perfect hotel, vacation rental, resort, apartment, guest house, or tree house, we’ve got you covered. </p><button class="button">Read more</button></div></div><div><div class="about__img h-full"><div class="even-columns h-full"><!--[-->`);
      ssrRenderList(unref(imagesList), (image, i2) => {
        _push(`<div class="set-bg-img h-full" style="${ssrRenderStyle(_ctx.$getBgStyle(image))}"></div>`);
      });
      _push(`<!--]--></div></div></div></div></div></section>`);
    };
  }
};
const _sfc_setup$8 = _sfc_main$8.setup;
_sfc_main$8.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Guest/About-section.vue");
  return _sfc_setup$8 ? _sfc_setup$8(props, ctx) : void 0;
};
const _export_sfc = (sfc, props) => {
  const target = sfc.__vccOpts || sfc;
  for (const [key, val] of props) {
    target[key] = val;
  }
  return target;
};
const _sfc_main$7 = {};
function _sfc_ssrRender$3(_ctx, _push, _parent, _attrs) {
  const _component_SvgSprite = resolveComponent("SvgSprite");
  _push(`<section${ssrRenderAttrs(mergeProps({ class: "services-section | padding-block-1000" }, _attrs))}><div class="container"><div class="services__title | text-center flow" style="${ssrRenderStyle({ "--flow-spacer": "1rem" })}"><span class="label">What we do</span><h3 class="fs-secondary-heading">Discover Our Services</h3></div><div class="services__content | grid gap-4 sm:grid-cols-2 lg:grid-cols-3 | padding-block-800"><div class="service-item"><a href="" class="algin-center text-center flow" style="${ssrRenderStyle({ "--flow-spacer": "1rem" })}"><div class="justify-center align-center">`);
  _push(ssrRenderComponent(_component_SvgSprite, {
    symbol: "travel-plan",
    role: "presentation",
    class: "icon service-icon"
  }, null, _parent));
  _push(`</div><h5 class="service-item__title fs-normal-heading">Travel Plan</h5><p> Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna. </p></a></div><div class="service-item | text-center flow" style="${ssrRenderStyle({ "--flow-spacer": "1rem" })}"><a href="" class="algin-center text-center flow" style="${ssrRenderStyle({ "--flow-spacer": "1rem" })}"><div class="justify-center align-center">`);
  _push(ssrRenderComponent(_component_SvgSprite, {
    symbol: "catering",
    role: "presentation",
    class: "icon service-icon"
  }, null, _parent));
  _push(`</div><h5 class="service-item__title fs-normal-heading">Catering Service</h5><p> Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna. </p></a></div><div class="service-item | text-center flow" style="${ssrRenderStyle({ "--flow-spacer": "1rem" })}"><a href="" class="algin-center text-center flow" style="${ssrRenderStyle({ "--flow-spacer": "1rem" })}"><div class="justify-center align-center">`);
  _push(ssrRenderComponent(_component_SvgSprite, {
    symbol: "babysitting",
    role: "presentation",
    class: "icon service-icon"
  }, null, _parent));
  _push(`</div><h5 class="service-item__title fs-normal-heading">Babysitting</h5><p> Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna. </p></a></div><div class="service-item | text-center flow" style="${ssrRenderStyle({ "--flow-spacer": "1rem" })}"><a href="" class="algin-center text-center flow" style="${ssrRenderStyle({ "--flow-spacer": "1rem" })}"><div class="justify-center align-center">`);
  _push(ssrRenderComponent(_component_SvgSprite, {
    symbol: "laundry",
    role: "presentation",
    class: "icon service-icon"
  }, null, _parent));
  _push(`</div><h5 class="service-item__title fs-normal-heading">Laundry</h5><p> Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna. </p></a></div><div class="service-item | text-center flow" style="${ssrRenderStyle({ "--flow-spacer": "1rem" })}"><a href="" class="algin-center text-center flow" style="${ssrRenderStyle({ "--flow-spacer": "1rem" })}"><div class="justify-center align-center">`);
  _push(ssrRenderComponent(_component_SvgSprite, {
    symbol: "driver",
    role: "presentation",
    class: "icon service-icon"
  }, null, _parent));
  _push(`</div><h5 class="service-item__title fs-normal-heading">Hire Driver</h5><p> Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna. </p></a></div><div class="service-item | text-center flow" style="${ssrRenderStyle({ "--flow-spacer": "1rem" })}"><a href="" class="algin-center text-center flow" style="${ssrRenderStyle({ "--flow-spacer": "1rem" })}"><div class="justify-center align-center">`);
  _push(ssrRenderComponent(_component_SvgSprite, {
    symbol: "bar-drink",
    role: "presentation",
    class: "icon service-icon"
  }, null, _parent));
  _push(`</div><h5 class="service-item__title fs-normal-heading">Bar &amp; Drink</h5><p> Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna. </p></a></div></div></div></section>`);
}
const _sfc_setup$7 = _sfc_main$7.setup;
_sfc_main$7.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Guest/Services-section.vue");
  return _sfc_setup$7 ? _sfc_setup$7(props, ctx) : void 0;
};
const ServicesSection = /* @__PURE__ */ _export_sfc(_sfc_main$7, [["ssrRender", _sfc_ssrRender$3]]);
const _sfc_main$6 = {
  __name: "Room-section",
  __ssrInlineRender: true,
  setup(__props) {
    let topRoomTypeList = reactive([
      {
        name: "Double Room",
        pricePerNight: 199,
        size: { value: 300, unit: "ft²" },
        capacity: 5,
        services: ["wifi", "TV", "bathroom"],
        imgUrl: "/img/room/room-b1.jpg"
      },
      {
        name: "Premium King Room",
        pricePerNight: 159,
        size: { value: 280, unit: "ft²" },
        capacity: 5,
        services: ["wifi", "TV", "bathroom"],
        imgUrl: "/img/room/room-b2.jpg"
      },
      {
        name: "Deluxe Room",
        pricePerNight: 198,
        size: { value: 350, unit: "ft²" },
        capacity: 5,
        services: ["wifi", "TV", "bathroom", "balcony"],
        imgUrl: "/img/room/room-b3.jpg"
      },
      {
        name: "Family Room",
        pricePerNight: 299,
        size: { value: 400, unit: "ft²" },
        capacity: 5,
        services: ["wifi", "TV", "bathroom", "kitchenette"],
        imgUrl: "/img/room/room-b4.jpg"
      }
    ]);
    function roomTypeDesc(room) {
      return Object.fromEntries(Object.entries(room).filter(([key]) => !["name", "pricePerNight", "imgUrl"].includes(key)));
    }
    function formatValue(value) {
      if (Array.isArray(value)) return value.map(capitalize).join(", ");
      if (typeof value === "object" && value !== null) return `${value.value} ${value.unit}`;
      return value;
    }
    function capitalize(str) {
      if (!str) return "";
      return str.charAt(0).toUpperCase() + str.slice(1);
    }
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<section${ssrRenderAttrs(mergeProps({ class: "room-section | padding-block-600" }, _attrs))}><div class="container-fluid"><div class="grid gap-x-3 gap-y-3 md:grid-cols-2 lg:grid-cols-4"><!--[-->`);
      ssrRenderList(unref(topRoomTypeList), (roomType) => {
        _push(`<div class="room-item | set-bg-img pop-up" style="${ssrRenderStyle(_ctx.$getBgStyle(roomType.imgUrl))}"><div class="room-item__content | item-pop-up padding-block-400 flow" style="${ssrRenderStyle({ "--flow-spacer": "1em" })}"><h3 class="room-item__title | text-center fs-normal-heading">${ssrInterpolate(roomType.name)}</h3><h2 class="room-item__price | text-center">${ssrInterpolate(roomType.pricePerNight)}$ <span>Per Night</span></h2><table class="room-item__desc"><tbody class="flow" style="${ssrRenderStyle({ "--flow-spacer": "1em" })}"><!--[-->`);
        ssrRenderList(roomTypeDesc(roomType), (value, key) => {
          _push(`<tr><td class="feature">${ssrInterpolate(capitalize(key))}:</td><td>${ssrInterpolate(formatValue(value))}</td></tr>`);
        });
        _push(`<!--]--></tbody></table></div></div>`);
      });
      _push(`<!--]--></div></div></section>`);
    };
  }
};
const _sfc_setup$6 = _sfc_main$6.setup;
_sfc_main$6.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Guest/Room-section.vue");
  return _sfc_setup$6 ? _sfc_setup$6(props, ctx) : void 0;
};
const _sfc_main$5 = {
  __name: "Home",
  __ssrInlineRender: true,
  setup(__props) {
    const page = usePage();
    const pageTitle = ` | ${page.component.replace(/^(Guest\/|Admin\/)/, "")} Page`;
    return (_ctx, _push, _parent, _attrs) => {
      const _component_Head = resolveComponent("Head");
      _push(`<!--[-->`);
      _push(ssrRenderComponent(_component_Head, { title: pageTitle }, null, _parent));
      _push(ssrRenderComponent(_sfc_main$9, null, null, _parent));
      _push(ssrRenderComponent(_sfc_main$8, null, null, _parent));
      _push(ssrRenderComponent(ServicesSection, null, null, _parent));
      _push(ssrRenderComponent(_sfc_main$6, null, null, _parent));
      _push(`<!--]-->`);
    };
  }
};
const _sfc_setup$5 = _sfc_main$5.setup;
_sfc_main$5.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Guest/Home.vue");
  return _sfc_setup$5 ? _sfc_setup$5(props, ctx) : void 0;
};
const __vite_glob_0_2 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: _sfc_main$5
}, Symbol.toStringTag, { value: "Module" }));
function t() {
  return t = Object.assign ? Object.assign.bind() : function(t3) {
    for (var e2 = 1; e2 < arguments.length; e2++) {
      var o2 = arguments[e2];
      for (var n2 in o2) ({}).hasOwnProperty.call(o2, n2) && (t3[n2] = o2[n2]);
    }
    return t3;
  }, t.apply(null, arguments);
}
const e = String.prototype.replace, o = /%20/g, n = { RFC1738: function(t3) {
  return e.call(t3, o, "+");
}, RFC3986: function(t3) {
  return String(t3);
} };
var r = "RFC3986";
const i = Object.prototype.hasOwnProperty, s = Array.isArray, u = (function() {
  const t3 = [];
  for (let e2 = 0; e2 < 256; ++e2) t3.push("%" + ((e2 < 16 ? "0" : "") + e2.toString(16)).toUpperCase());
  return t3;
})(), l = function t2(e2, o2, n2) {
  if (!o2) return e2;
  if ("object" != typeof o2) {
    if (s(e2)) e2.push(o2);
    else {
      if (!e2 || "object" != typeof e2) return [e2, o2];
      (n2 && (n2.plainObjects || n2.allowPrototypes) || !i.call(Object.prototype, o2)) && (e2[o2] = true);
    }
    return e2;
  }
  if (!e2 || "object" != typeof e2) return [e2].concat(o2);
  let r2 = e2;
  return s(e2) && !s(o2) && (r2 = (function(t3, e3) {
    const o3 = e3 && e3.plainObjects ? /* @__PURE__ */ Object.create(null) : {};
    for (let e4 = 0; e4 < t3.length; ++e4) void 0 !== t3[e4] && (o3[e4] = t3[e4]);
    return o3;
  })(e2, n2)), s(e2) && s(o2) ? (o2.forEach(function(o3, r3) {
    if (i.call(e2, r3)) {
      const i2 = e2[r3];
      i2 && "object" == typeof i2 && o3 && "object" == typeof o3 ? e2[r3] = t2(i2, o3, n2) : e2.push(o3);
    } else e2[r3] = o3;
  }), e2) : Object.keys(o2).reduce(function(e3, r3) {
    const s2 = o2[r3];
    return e3[r3] = i.call(e3, r3) ? t2(e3[r3], s2, n2) : s2, e3;
  }, r2);
}, c = 1024, a = function(t3, e2) {
  return [].concat(t3, e2);
}, f = function(t3, e2) {
  if (s(t3)) {
    const o2 = [];
    for (let n2 = 0; n2 < t3.length; n2 += 1) o2.push(e2(t3[n2]));
    return o2;
  }
  return e2(t3);
}, p = Object.prototype.hasOwnProperty, y = { brackets: function(t3) {
  return t3 + "[]";
}, comma: "comma", indices: function(t3, e2) {
  return t3 + "[" + e2 + "]";
}, repeat: function(t3) {
  return t3;
} }, d = Array.isArray, h = Array.prototype.push, b = function(t3, e2) {
  h.apply(t3, d(e2) ? e2 : [e2]);
}, m = Date.prototype.toISOString, g = { addQueryPrefix: false, allowDots: false, allowEmptyArrays: false, arrayFormat: "indices", charset: "utf-8", charsetSentinel: false, delimiter: "&", encode: true, encodeDotInKeys: false, encoder: function(t3, e2, o2, n2, r2) {
  if (0 === t3.length) return t3;
  let i2 = t3;
  if ("symbol" == typeof t3 ? i2 = Symbol.prototype.toString.call(t3) : "string" != typeof t3 && (i2 = String(t3)), "iso-8859-1" === o2) return escape(i2).replace(/%u[0-9a-f]{4}/gi, function(t4) {
    return "%26%23" + parseInt(t4.slice(2), 16) + "%3B";
  });
  let s2 = "";
  for (let t4 = 0; t4 < i2.length; t4 += c) {
    const e3 = i2.length >= c ? i2.slice(t4, t4 + c) : i2, o3 = [];
    for (let t5 = 0; t5 < e3.length; ++t5) {
      let n3 = e3.charCodeAt(t5);
      45 === n3 || 46 === n3 || 95 === n3 || 126 === n3 || n3 >= 48 && n3 <= 57 || n3 >= 65 && n3 <= 90 || n3 >= 97 && n3 <= 122 || "RFC1738" === r2 && (40 === n3 || 41 === n3) ? o3[o3.length] = e3.charAt(t5) : n3 < 128 ? o3[o3.length] = u[n3] : n3 < 2048 ? o3[o3.length] = u[192 | n3 >> 6] + u[128 | 63 & n3] : n3 < 55296 || n3 >= 57344 ? o3[o3.length] = u[224 | n3 >> 12] + u[128 | n3 >> 6 & 63] + u[128 | 63 & n3] : (t5 += 1, n3 = 65536 + ((1023 & n3) << 10 | 1023 & e3.charCodeAt(t5)), o3[o3.length] = u[240 | n3 >> 18] + u[128 | n3 >> 12 & 63] + u[128 | n3 >> 6 & 63] + u[128 | 63 & n3]);
    }
    s2 += o3.join("");
  }
  return s2;
}, encodeValuesOnly: false, format: r, formatter: n[r], indices: false, serializeDate: function(t3) {
  return m.call(t3);
}, skipNulls: false, strictNullHandling: false }, w = {}, v = function(t3, e2, o2, n2, r2, i2, s2, u2, l2, c2, a2, p2, y2, h2, m2, j2, $2, E2) {
  let O2 = t3, T2 = E2, R2 = 0, S2 = false;
  for (; void 0 !== (T2 = T2.get(w)) && !S2; ) {
    const e3 = T2.get(t3);
    if (R2 += 1, void 0 !== e3) {
      if (e3 === R2) throw new RangeError("Cyclic object value");
      S2 = true;
    }
    void 0 === T2.get(w) && (R2 = 0);
  }
  if ("function" == typeof c2 ? O2 = c2(e2, O2) : O2 instanceof Date ? O2 = y2(O2) : "comma" === o2 && d(O2) && (O2 = f(O2, function(t4) {
    return t4 instanceof Date ? y2(t4) : t4;
  })), null === O2) {
    if (i2) return l2 && !j2 ? l2(e2, g.encoder, $2, "key", h2) : e2;
    O2 = "";
  }
  if ("string" == typeof (I2 = O2) || "number" == typeof I2 || "boolean" == typeof I2 || "symbol" == typeof I2 || "bigint" == typeof I2 || (function(t4) {
    return !(!t4 || "object" != typeof t4 || !(t4.constructor && t4.constructor.isBuffer && t4.constructor.isBuffer(t4)));
  })(O2)) return l2 ? [m2(j2 ? e2 : l2(e2, g.encoder, $2, "key", h2)) + "=" + m2(l2(O2, g.encoder, $2, "value", h2))] : [m2(e2) + "=" + m2(String(O2))];
  var I2;
  const A2 = [];
  if (void 0 === O2) return A2;
  let D2;
  if ("comma" === o2 && d(O2)) j2 && l2 && (O2 = f(O2, l2)), D2 = [{ value: O2.length > 0 ? O2.join(",") || null : void 0 }];
  else if (d(c2)) D2 = c2;
  else {
    const t4 = Object.keys(O2);
    D2 = a2 ? t4.sort(a2) : t4;
  }
  const _2 = u2 ? e2.replace(/\./g, "%2E") : e2, k = n2 && d(O2) && 1 === O2.length ? _2 + "[]" : _2;
  if (r2 && d(O2) && 0 === O2.length) return k + "[]";
  for (let e3 = 0; e3 < D2.length; ++e3) {
    const f2 = D2[e3], g2 = "object" == typeof f2 && void 0 !== f2.value ? f2.value : O2[f2];
    if (s2 && null === g2) continue;
    const T3 = p2 && u2 ? f2.replace(/\./g, "%2E") : f2, S3 = d(O2) ? "function" == typeof o2 ? o2(k, T3) : k : k + (p2 ? "." + T3 : "[" + T3 + "]");
    E2.set(t3, R2);
    const I3 = /* @__PURE__ */ new WeakMap();
    I3.set(w, E2), b(A2, v(g2, S3, o2, n2, r2, i2, s2, u2, "comma" === o2 && j2 && d(O2) ? null : l2, c2, a2, p2, y2, h2, m2, j2, $2, I3));
  }
  return A2;
}, j = Object.prototype.hasOwnProperty, $ = Array.isArray, E = { allowDots: false, allowEmptyArrays: false, allowPrototypes: false, allowSparse: false, arrayLimit: 20, charset: "utf-8", charsetSentinel: false, comma: false, decodeDotInKeys: false, decoder: function(t3, e2, o2) {
  const n2 = t3.replace(/\+/g, " ");
  if ("iso-8859-1" === o2) return n2.replace(/%[0-9a-f]{2}/gi, unescape);
  try {
    return decodeURIComponent(n2);
  } catch (t4) {
    return n2;
  }
}, delimiter: "&", depth: 5, duplicates: "combine", ignoreQueryPrefix: false, interpretNumericEntities: false, parameterLimit: 1e3, parseArrays: true, plainObjects: false, strictNullHandling: false }, O = function(t3) {
  return t3.replace(/&#(\d+);/g, function(t4, e2) {
    return String.fromCharCode(parseInt(e2, 10));
  });
}, T = function(t3, e2) {
  return t3 && "string" == typeof t3 && e2.comma && t3.indexOf(",") > -1 ? t3.split(",") : t3;
}, R = function(t3, e2, o2, n2) {
  if (!t3) return;
  const r2 = o2.allowDots ? t3.replace(/\.([^.[]+)/g, "[$1]") : t3, i2 = /(\[[^[\]]*])/g;
  let s2 = o2.depth > 0 && /(\[[^[\]]*])/.exec(r2);
  const u2 = s2 ? r2.slice(0, s2.index) : r2, l2 = [];
  if (u2) {
    if (!o2.plainObjects && j.call(Object.prototype, u2) && !o2.allowPrototypes) return;
    l2.push(u2);
  }
  let c2 = 0;
  for (; o2.depth > 0 && null !== (s2 = i2.exec(r2)) && c2 < o2.depth; ) {
    if (c2 += 1, !o2.plainObjects && j.call(Object.prototype, s2[1].slice(1, -1)) && !o2.allowPrototypes) return;
    l2.push(s2[1]);
  }
  return s2 && l2.push("[" + r2.slice(s2.index) + "]"), (function(t4, e3, o3, n3) {
    let r3 = n3 ? e3 : T(e3, o3);
    for (let e4 = t4.length - 1; e4 >= 0; --e4) {
      let n4;
      const i3 = t4[e4];
      if ("[]" === i3 && o3.parseArrays) n4 = o3.allowEmptyArrays && "" === r3 ? [] : [].concat(r3);
      else {
        n4 = o3.plainObjects ? /* @__PURE__ */ Object.create(null) : {};
        const t5 = "[" === i3.charAt(0) && "]" === i3.charAt(i3.length - 1) ? i3.slice(1, -1) : i3, e5 = o3.decodeDotInKeys ? t5.replace(/%2E/g, ".") : t5, s3 = parseInt(e5, 10);
        o3.parseArrays || "" !== e5 ? !isNaN(s3) && i3 !== e5 && String(s3) === e5 && s3 >= 0 && o3.parseArrays && s3 <= o3.arrayLimit ? (n4 = [], n4[s3] = r3) : "__proto__" !== e5 && (n4[e5] = r3) : n4 = { 0: r3 };
      }
      r3 = n4;
    }
    return r3;
  })(l2, e2, o2, n2);
};
function S(t3, e2) {
  const o2 = /* @__PURE__ */ (function(t4) {
    return E;
  })();
  if ("" === t3 || null == t3) return o2.plainObjects ? /* @__PURE__ */ Object.create(null) : {};
  const n2 = "string" == typeof t3 ? (function(t4, e3) {
    const o3 = { __proto__: null }, n3 = (e3.ignoreQueryPrefix ? t4.replace(/^\?/, "") : t4).split(e3.delimiter, Infinity === e3.parameterLimit ? void 0 : e3.parameterLimit);
    let r3, i3 = -1, s2 = e3.charset;
    if (e3.charsetSentinel) for (r3 = 0; r3 < n3.length; ++r3) 0 === n3[r3].indexOf("utf8=") && ("utf8=%E2%9C%93" === n3[r3] ? s2 = "utf-8" : "utf8=%26%2310003%3B" === n3[r3] && (s2 = "iso-8859-1"), i3 = r3, r3 = n3.length);
    for (r3 = 0; r3 < n3.length; ++r3) {
      if (r3 === i3) continue;
      const t5 = n3[r3], u2 = t5.indexOf("]="), l2 = -1 === u2 ? t5.indexOf("=") : u2 + 1;
      let c2, p2;
      -1 === l2 ? (c2 = e3.decoder(t5, E.decoder, s2, "key"), p2 = e3.strictNullHandling ? null : "") : (c2 = e3.decoder(t5.slice(0, l2), E.decoder, s2, "key"), p2 = f(T(t5.slice(l2 + 1), e3), function(t6) {
        return e3.decoder(t6, E.decoder, s2, "value");
      })), p2 && e3.interpretNumericEntities && "iso-8859-1" === s2 && (p2 = O(p2)), t5.indexOf("[]=") > -1 && (p2 = $(p2) ? [p2] : p2);
      const y2 = j.call(o3, c2);
      y2 && "combine" === e3.duplicates ? o3[c2] = a(o3[c2], p2) : y2 && "last" !== e3.duplicates || (o3[c2] = p2);
    }
    return o3;
  })(t3, o2) : t3;
  let r2 = o2.plainObjects ? /* @__PURE__ */ Object.create(null) : {};
  const i2 = Object.keys(n2);
  for (let e3 = 0; e3 < i2.length; ++e3) {
    const s2 = i2[e3], u2 = R(s2, n2[s2], o2, "string" == typeof t3);
    r2 = l(r2, u2, o2);
  }
  return true === o2.allowSparse ? r2 : (function(t4) {
    const e3 = [{ obj: { o: t4 }, prop: "o" }], o3 = [];
    for (let t5 = 0; t5 < e3.length; ++t5) {
      const n3 = e3[t5], r3 = n3.obj[n3.prop], i3 = Object.keys(r3);
      for (let t6 = 0; t6 < i3.length; ++t6) {
        const n4 = i3[t6], s2 = r3[n4];
        "object" == typeof s2 && null !== s2 && -1 === o3.indexOf(s2) && (e3.push({ obj: r3, prop: n4 }), o3.push(s2));
      }
    }
    return (function(t5) {
      for (; t5.length > 1; ) {
        const e4 = t5.pop(), o4 = e4.obj[e4.prop];
        if (s(o4)) {
          const t6 = [];
          for (let e5 = 0; e5 < o4.length; ++e5) void 0 !== o4[e5] && t6.push(o4[e5]);
          e4.obj[e4.prop] = t6;
        }
      }
    })(e3), t4;
  })(r2);
}
class I {
  constructor(t3, e2, o2) {
    var n2, r2;
    this.name = t3, this.definition = e2, this.bindings = null != (n2 = e2.bindings) ? n2 : {}, this.wheres = null != (r2 = e2.wheres) ? r2 : {}, this.config = o2;
  }
  get template() {
    const t3 = `${this.origin}/${this.definition.uri}`.replace(/\/+$/, "");
    return "" === t3 ? "/" : t3;
  }
  get origin() {
    return this.config.absolute ? this.definition.domain ? `${this.config.url.match(/^\w+:\/\//)[0]}${this.definition.domain}${this.config.port ? `:${this.config.port}` : ""}` : this.config.url : "";
  }
  get parameterSegments() {
    var t3, e2;
    return null != (t3 = null == (e2 = this.template.match(/{[^}?]+\??}/g)) ? void 0 : e2.map((t4) => ({ name: t4.replace(/{|\??}/g, ""), required: !/\?}$/.test(t4) }))) ? t3 : [];
  }
  matchesUrl(t3) {
    var e2;
    if (!this.definition.methods.includes("GET")) return false;
    const o2 = this.template.replace(/[.*+$()[\]]/g, "\\$&").replace(/(\/?){([^}?]*)(\??)}/g, (t4, e3, o3, n3) => {
      var r3;
      const i3 = `(?<${o3}>${(null == (r3 = this.wheres[o3]) ? void 0 : r3.replace(/(^\^)|(\$$)/g, "")) || "[^/?]+"})`;
      return n3 ? `(${e3}${i3})?` : `${e3}${i3}`;
    }).replace(/^\w+:\/\//, ""), [n2, r2] = t3.replace(/^\w+:\/\//, "").split("?"), i2 = null != (e2 = new RegExp(`^${o2}/?$`).exec(n2)) ? e2 : new RegExp(`^${o2}/?$`).exec(decodeURI(n2));
    if (i2) {
      for (const t4 in i2.groups) i2.groups[t4] = "string" == typeof i2.groups[t4] ? decodeURIComponent(i2.groups[t4]) : i2.groups[t4];
      return { params: i2.groups, query: S(r2) };
    }
    return false;
  }
  compile(t3) {
    return this.parameterSegments.length ? this.template.replace(/{([^}?]+)(\??)}/g, (e2, o2, n2) => {
      var r2, i2;
      if (!n2 && [null, void 0].includes(t3[o2])) throw new Error(`Ziggy error: '${o2}' parameter is required for route '${this.name}'.`);
      if (this.wheres[o2] && !new RegExp(`^${n2 ? `(${this.wheres[o2]})?` : this.wheres[o2]}$`).test(null != (i2 = t3[o2]) ? i2 : "")) throw new Error(`Ziggy error: '${o2}' parameter '${t3[o2]}' does not match required format '${this.wheres[o2]}' for route '${this.name}'.`);
      return encodeURI(null != (r2 = t3[o2]) ? r2 : "").replace(/%7C/g, "|").replace(/%25/g, "%").replace(/\$/g, "%24");
    }).replace(this.config.absolute ? /(\.[^/]+?)(\/\/)/ : /(^)(\/\/)/, "$1/").replace(/\/+$/, "") : this.template;
  }
}
class A extends String {
  constructor(e2, o2, n2 = true, r2) {
    if (super(), this.t = null != r2 ? r2 : "undefined" != typeof Ziggy ? Ziggy : null == globalThis ? void 0 : globalThis.Ziggy, !this.t && "undefined" != typeof document && document.getElementById("ziggy-routes-json") && (globalThis.Ziggy = JSON.parse(document.getElementById("ziggy-routes-json").textContent), this.t = globalThis.Ziggy), this.t = t({}, this.t, { absolute: n2 }), e2) {
      if (!this.t.routes[e2]) throw new Error(`Ziggy error: route '${e2}' is not in the route list.`);
      this.i = new I(e2, this.t.routes[e2], this.t), this.u = this.l(o2);
    }
  }
  toString() {
    const e2 = Object.keys(this.u).filter((t3) => !this.i.parameterSegments.some(({ name: e3 }) => e3 === t3)).filter((t3) => "_query" !== t3).reduce((e3, o2) => t({}, e3, { [o2]: this.u[o2] }), {});
    return this.i.compile(this.u) + (function(t3, e3) {
      let o2 = t3;
      const i2 = (function(t4) {
        if (!t4) return g;
        if (void 0 !== t4.allowEmptyArrays && "boolean" != typeof t4.allowEmptyArrays) throw new TypeError("`allowEmptyArrays` option can only be `true` or `false`, when provided");
        if (void 0 !== t4.encodeDotInKeys && "boolean" != typeof t4.encodeDotInKeys) throw new TypeError("`encodeDotInKeys` option can only be `true` or `false`, when provided");
        if (null != t4.encoder && "function" != typeof t4.encoder) throw new TypeError("Encoder has to be a function.");
        const e4 = t4.charset || g.charset;
        if (void 0 !== t4.charset && "utf-8" !== t4.charset && "iso-8859-1" !== t4.charset) throw new TypeError("The charset option must be either utf-8, iso-8859-1, or undefined");
        let o3 = r;
        if (void 0 !== t4.format) {
          if (!p.call(n, t4.format)) throw new TypeError("Unknown format option provided.");
          o3 = t4.format;
        }
        const i3 = n[o3];
        let s3, u3 = g.filter;
        if (("function" == typeof t4.filter || d(t4.filter)) && (u3 = t4.filter), s3 = t4.arrayFormat in y ? t4.arrayFormat : "indices" in t4 ? t4.indices ? "indices" : "repeat" : g.arrayFormat, "commaRoundTrip" in t4 && "boolean" != typeof t4.commaRoundTrip) throw new TypeError("`commaRoundTrip` must be a boolean, or absent");
        return { addQueryPrefix: "boolean" == typeof t4.addQueryPrefix ? t4.addQueryPrefix : g.addQueryPrefix, allowDots: void 0 === t4.allowDots ? true === t4.encodeDotInKeys || g.allowDots : !!t4.allowDots, allowEmptyArrays: "boolean" == typeof t4.allowEmptyArrays ? !!t4.allowEmptyArrays : g.allowEmptyArrays, arrayFormat: s3, charset: e4, charsetSentinel: "boolean" == typeof t4.charsetSentinel ? t4.charsetSentinel : g.charsetSentinel, commaRoundTrip: t4.commaRoundTrip, delimiter: void 0 === t4.delimiter ? g.delimiter : t4.delimiter, encode: "boolean" == typeof t4.encode ? t4.encode : g.encode, encodeDotInKeys: "boolean" == typeof t4.encodeDotInKeys ? t4.encodeDotInKeys : g.encodeDotInKeys, encoder: "function" == typeof t4.encoder ? t4.encoder : g.encoder, encodeValuesOnly: "boolean" == typeof t4.encodeValuesOnly ? t4.encodeValuesOnly : g.encodeValuesOnly, filter: u3, format: o3, formatter: i3, serializeDate: "function" == typeof t4.serializeDate ? t4.serializeDate : g.serializeDate, skipNulls: "boolean" == typeof t4.skipNulls ? t4.skipNulls : g.skipNulls, sort: "function" == typeof t4.sort ? t4.sort : null, strictNullHandling: "boolean" == typeof t4.strictNullHandling ? t4.strictNullHandling : g.strictNullHandling };
      })(e3);
      let s2, u2;
      "function" == typeof i2.filter ? (u2 = i2.filter, o2 = u2("", o2)) : d(i2.filter) && (u2 = i2.filter, s2 = u2);
      const l2 = [];
      if ("object" != typeof o2 || null === o2) return "";
      const c2 = y[i2.arrayFormat], a2 = "comma" === c2 && i2.commaRoundTrip;
      s2 || (s2 = Object.keys(o2)), i2.sort && s2.sort(i2.sort);
      const f2 = /* @__PURE__ */ new WeakMap();
      for (let t4 = 0; t4 < s2.length; ++t4) {
        const e4 = s2[t4];
        i2.skipNulls && null === o2[e4] || b(l2, v(o2[e4], e4, c2, a2, i2.allowEmptyArrays, i2.strictNullHandling, i2.skipNulls, i2.encodeDotInKeys, i2.encode ? i2.encoder : null, i2.filter, i2.sort, i2.allowDots, i2.serializeDate, i2.format, i2.formatter, i2.encodeValuesOnly, i2.charset, f2));
      }
      const h2 = l2.join(i2.delimiter);
      let m2 = true === i2.addQueryPrefix ? "?" : "";
      return i2.charsetSentinel && (m2 += "iso-8859-1" === i2.charset ? "utf8=%26%2310003%3B&" : "utf8=%E2%9C%93&"), h2.length > 0 ? m2 + h2 : "";
    })(t({}, e2, this.u._query), { addQueryPrefix: true, arrayFormat: "indices", encodeValuesOnly: true, skipNulls: true, encoder: (t3, e3) => "boolean" == typeof t3 ? Number(t3) : e3(t3) });
  }
  p(e2) {
    e2 ? this.t.absolute && e2.startsWith("/") && (e2 = this.h().host + e2) : e2 = this.m();
    let o2 = {};
    const [n2, r2] = Object.entries(this.t.routes).find(([t3, n3]) => o2 = new I(t3, n3, this.t).matchesUrl(e2)) || [void 0, void 0];
    return t({ name: n2 }, o2, { route: r2 });
  }
  m() {
    const { host: t3, pathname: e2, search: o2 } = this.h();
    return (this.t.absolute ? t3 + e2 : e2.replace(this.t.url.replace(/^\w*:\/\/[^/]+/, ""), "").replace(/^\/+/, "/")) + o2;
  }
  current(e2, o2) {
    const { name: n2, params: r2, query: i2, route: s2 } = this.p();
    if (!e2) return n2;
    const u2 = new RegExp(`^${e2.replace(/\./g, "\\.").replace(/\*/g, ".*")}$`).test(n2);
    if ([null, void 0].includes(o2) || !u2) return u2;
    const l2 = new I(n2, s2, this.t);
    o2 = this.l(o2, l2);
    const c2 = t({}, r2, i2);
    if (Object.values(o2).every((t3) => !t3) && !Object.values(c2).some((t3) => void 0 !== t3)) return true;
    const a2 = (t3, e3) => Object.entries(t3).every(([t4, o3]) => Array.isArray(o3) && Array.isArray(e3[t4]) ? o3.every((o4) => e3[t4].includes(o4) || e3[t4].includes(decodeURIComponent(o4))) : "object" == typeof o3 && "object" == typeof e3[t4] && null !== o3 && null !== e3[t4] ? a2(o3, e3[t4]) : e3[t4] == o3 || e3[t4] == decodeURIComponent(o3));
    return a2(o2, c2);
  }
  h() {
    var t3, e2, o2, n2, r2, i2;
    const { host: s2 = "", pathname: u2 = "", search: l2 = "" } = "undefined" != typeof window ? window.location : {};
    return { host: null != (t3 = null == (e2 = this.t.location) ? void 0 : e2.host) ? t3 : s2, pathname: null != (o2 = null == (n2 = this.t.location) ? void 0 : n2.pathname) ? o2 : u2, search: null != (r2 = null == (i2 = this.t.location) ? void 0 : i2.search) ? r2 : l2 };
  }
  get params() {
    const { params: e2, query: o2 } = this.p();
    return t({}, e2, o2);
  }
  get routeParams() {
    return this.p().params;
  }
  get queryParams() {
    return this.p().query;
  }
  has(t3) {
    return this.t.routes.hasOwnProperty(t3);
  }
  l(e2 = {}, o2 = this.i) {
    null != e2 || (e2 = {}), e2 = ["string", "number"].includes(typeof e2) ? [e2] : e2;
    const n2 = o2.parameterSegments.filter(({ name: t3 }) => !this.t.defaults[t3]);
    return Array.isArray(e2) ? e2 = e2.reduce((e3, o3, r2) => t({}, e3, n2[r2] ? { [n2[r2].name]: o3 } : "object" == typeof o3 ? o3 : { [o3]: "" }), {}) : 1 !== n2.length || e2[n2[0].name] || !e2.hasOwnProperty(Object.values(o2.bindings)[0]) && !e2.hasOwnProperty("id") || (e2 = { [n2[0].name]: e2 }), t({}, this.v(o2), this.j(e2, o2));
  }
  v(e2) {
    return e2.parameterSegments.filter(({ name: t3 }) => this.t.defaults[t3]).reduce((e3, { name: o2 }, n2) => t({}, e3, { [o2]: this.t.defaults[o2] }), {});
  }
  j(e2, { bindings: o2, parameterSegments: n2 }) {
    return Object.entries(e2).reduce((e3, [r2, i2]) => {
      if (!i2 || "object" != typeof i2 || Array.isArray(i2) || !n2.some(({ name: t3 }) => t3 === r2)) return t({}, e3, { [r2]: i2 });
      if (!i2.hasOwnProperty(o2[r2])) {
        if (!i2.hasOwnProperty("id")) throw new Error(`Ziggy error: object passed as '${r2}' parameter is missing route model binding key '${o2[r2]}'.`);
        o2[r2] = "id";
      }
      return t({}, e3, { [r2]: i2[o2[r2]] });
    }, {});
  }
  valueOf() {
    return this.toString();
  }
}
function D(t3, e2, o2, n2) {
  const r2 = new A(t3, e2, o2, n2);
  return t3 ? r2.toString() : r2;
}
const _ = { install(t3, e2) {
  const o2 = (t4, o3, n2, r2 = e2) => D(t4, o3, n2, r2);
  parseInt(t3.version) > 2 ? (t3.config.globalProperties.route = o2, t3.provide("route", o2)) : t3.mixin({ methods: { route: o2 } });
} };
const _imports_0$2 = "/build/assets/united-states-of-america-DeWklOeA.png";
const _imports_0$1 = "/build/assets/logo-FkPooXul.png";
const _sfc_main$4 = {};
function _sfc_ssrRender$2(_ctx, _push, _parent, _attrs) {
  const _component_SvgSprite = resolveComponent("SvgSprite");
  const _component_Link = resolveComponent("Link");
  _push(`<header${ssrRenderAttrs(mergeProps({ class: "primary-header" }, _attrs))}><section class="top-nav | padding-block-200 section-divider"><div class="container"><div class="even-columns"><nav class="top-nav__left"><ul class="nav-list" role="list"><li class="padding-block-200">`);
  _push(ssrRenderComponent(_component_SvgSprite, {
    symbol: "telephone-fill",
    size: "0 0 24 24",
    role: "presentation",
    class: "icon"
  }, null, _parent));
  _push(`<span class="fw-semi-bold">037 619 3244</span></li><li>`);
  _push(ssrRenderComponent(_component_SvgSprite, {
    symbol: "envelope-fill",
    size: "0 0 24 24",
    role: "presentation",
    class: "icon"
  }, null, _parent));
  _push(`<span class="fw-semi-bold">peterho5477@gmail.com</span></li></ul></nav><nav class="top-nav__right"><div class="nav-wrapper"><ul class="social-list" role="list"><li><a href="">`);
  _push(ssrRenderComponent(_component_SvgSprite, {
    symbol: "facebook",
    size: "0 0 24 24",
    role: "presentation",
    class: "icon social-icon"
  }, null, _parent));
  _push(`</a></li><li><a href="">`);
  _push(ssrRenderComponent(_component_SvgSprite, {
    symbol: "instagram",
    size: "0 0 24 24",
    role: "presentation",
    class: "icon social-icon"
  }, null, _parent));
  _push(`</a></li><li><a href="">`);
  _push(ssrRenderComponent(_component_SvgSprite, {
    symbol: "twitch",
    size: "0 0 24 24",
    role: "presentation",
    class: "icon social-icon"
  }, null, _parent));
  _push(`</a></li><li><a href="">`);
  _push(ssrRenderComponent(_component_SvgSprite, {
    symbol: "twitter-x",
    size: "0 0 24 24",
    role: "presentation",
    class: "icon social-icon"
  }, null, _parent));
  _push(`</a></li></ul><button class="button">Book now</button><div class="toggle-lang-switch"><img class="flag-icon"${ssrRenderAttr("src", _imports_0$2)} alt=""><div class="current-lang"><span> EN </span>`);
  _push(ssrRenderComponent(_component_SvgSprite, {
    symbol: "caret-down-fill",
    size: "0 0 24 24",
    role: "presentation",
    class: "icon"
  }, null, _parent));
  _push(`</div><ul class="lang-list | padding-block-200" role="list"><li class="en"><a href="">EN</a></li><li class="vi"><a href="">VI</a></li></ul></div></div></nav></div></div></section><section class="bottom-nav | padding-block-400"><div class="container"><div class="even-columns"><div class="header-logo"><img${ssrRenderAttr("src", _imports_0$1)} alt=""></div><nav class="primary-bottom-nav"><div class="search-bar"></div><ul class="nav-list" role="list"><li>`);
  _push(ssrRenderComponent(_component_Link, {
    href: _ctx.route("home")
  }, {
    default: withCtx((_2, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<span${_scopeId}>Home</span>`);
      } else {
        return [
          createVNode("span", null, "Home")
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`</li><li><a href=""><span>Rooms</span></a></li><li><a href=""><span>Blogs</span></a></li><li>`);
  _push(ssrRenderComponent(_component_Link, {
    href: _ctx.route("about")
  }, {
    default: withCtx((_2, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<span${_scopeId}>About us</span>`);
      } else {
        return [
          createVNode("span", null, "About us")
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`</li><li><a href=""><span>Contact</span></a></li><li><a href=""><span>Account</span></a></li></ul></nav></div></div></section></header>`);
}
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Guest/Header.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const Header$1 = /* @__PURE__ */ _export_sfc(_sfc_main$4, [["ssrRender", _sfc_ssrRender$2]]);
const _imports_0 = "/build/assets/footer-logo-DaodFKZn.png";
const _sfc_main$3 = {};
function _sfc_ssrRender$1(_ctx, _push, _parent, _attrs) {
  const _component_SvgSprite = resolveComponent("SvgSprite");
  _push(`<footer${ssrRenderAttrs(mergeProps({ class: "footer" }, _attrs))}><div class="footer-content | padding-block-600"><div class="container"><div class="footer-content__wrapper"><div class="footer-content__about | flow text-center-only-md" style="${ssrRenderStyle({ "--flow-spacer": "1em" })}"><img class="footer-logo"${ssrRenderAttr("src", _imports_0)} alt=""><p data-width="narrow"> We inspire and reach millions of travelers across 90 local websites </p><ul class="social-list | justify-center-only-md" data-type="inverted" data-size="big"><li><a href="">`);
  _push(ssrRenderComponent(_component_SvgSprite, {
    symbol: "facebook",
    size: "0 0 24 24",
    role: "presentation",
    class: "icon social-icon"
  }, null, _parent));
  _push(`</a></li><li><a href="">`);
  _push(ssrRenderComponent(_component_SvgSprite, {
    symbol: "instagram",
    size: "0 0 24 24",
    role: "presentation",
    class: "icon social-icon"
  }, null, _parent));
  _push(`</a></li><li><a href="">`);
  _push(ssrRenderComponent(_component_SvgSprite, {
    symbol: "twitch",
    size: "0 0 24 24",
    role: "presentation",
    class: "icon social-icon"
  }, null, _parent));
  _push(`</a></li><li><a href="">`);
  _push(ssrRenderComponent(_component_SvgSprite, {
    symbol: "twitter-x",
    size: "0 0 24 24",
    role: "presentation",
    class: "icon social-icon"
  }, null, _parent));
  _push(`</a></li></ul></div><div class="footer-content__contact | flow text-center-only-md" style="${ssrRenderStyle({ "--flow-spacer": "1em" })}"><span class="label">Contact us</span><ul class="contact-list"><div class="flow" style="${ssrRenderStyle({ "--flow-spacer": "0.75em" })}"><li>037 619 3244</li><li>peterho5477@gmail.com</li><li>12 Do Luong HCM city</li></div></ul></div><div class="footer-content__newsletter | flow text-center-only-md" style="${ssrRenderStyle({ "--flow-spacer": "1em" })}"><span class="label">New latest</span><p>Get the latest updates and offers</p><form action="" class="letter-form"><input type="text" placeholded="Email"><button class="button send-button">`);
  _push(ssrRenderComponent(_component_SvgSprite, {
    symbol: "send-letter",
    size: "0 0 24 24",
    role: "presentation",
    class: "icon"
  }, null, _parent));
  _push(`</button></form></div></div></div></div><div class="footer-copyright | padding-block-800"><div class="container"><div class="justify-center align-center"><ul class="policy-list | justify-center-only-md"><li>Contact</li><li>Terms of use</li><li>Privacy</li><li>Environmental Policy</li></ul></div><div class="footer-copyright__text | align-center justify-center mt-4"><p>Copyright @2025 made by Peter Ho</p></div></div></div></footer>`);
}
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Guest/Footer.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const Footer = /* @__PURE__ */ _export_sfc(_sfc_main$3, [["ssrRender", _sfc_ssrRender$1]]);
const _sfc_main$2 = {
  __name: "Guest",
  __ssrInlineRender: true,
  setup(__props) {
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "guest-layout" }, _attrs))}>`);
      _push(ssrRenderComponent(Header$1, null, null, _parent));
      _push(`<main>`);
      ssrRenderSlot(_ctx.$slots, "default", {}, null, _push, _parent);
      _push(`</main>`);
      _push(ssrRenderComponent(Footer, null, null, _parent));
      _push(`</div>`);
    };
  }
};
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Layouts/Guest.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs) {
  const _component_SvgSprite = resolveComponent("SvgSprite");
  _push(`<header${ssrRenderAttrs(mergeProps({ class: "primary-header" }, _attrs))}><section class="top-nav | padding-block-200"><div class="container"><div class="grid grid-cols-12"><div class="header-logo | col-span-12 lg:col-span-2"><img${ssrRenderAttr("src", _imports_0$1)} alt=""></div><div class="col-span-12 lg:col-span-10"><nav class="nav-wrapper"><ul class="financial-tools | nav-list | align-center" role="list"><li><a class="align-center" href="">`);
  _push(ssrRenderComponent(_component_SvgSprite, {
    symbol: "file-invoice-dollar",
    size: "0 0 24 24",
    role: "presentation",
    class: "icon"
  }, null, _parent));
  _push(`<span>Hóa đơn điện tử</span></a></li><li><a class="align-center" href="">`);
  _push(ssrRenderComponent(_component_SvgSprite, {
    symbol: "sack-dollar",
    size: "0 0 24 24",
    role: "presentation",
    class: "icon"
  }, null, _parent));
  _push(`<span>Vay vốn</span></a></li><li><a class="align-center" href="">`);
  _push(ssrRenderComponent(_component_SvgSprite, {
    symbol: "credit-card",
    size: "0 0 24 24",
    role: "presentation",
    class: "icon"
  }, null, _parent));
  _push(`<span>Thanh toán</span></a></li><li><a class="align-center" href="">`);
  _push(ssrRenderComponent(_component_SvgSprite, {
    symbol: "circle-infor",
    size: "0 0 24 24",
    role: "presentation",
    class: "icon"
  }, null, _parent));
  _push(`<span>Hỗ trợ</span></a></li></ul><ul class="setting-options | nav-list"><li class="branch | justify-center align-center">`);
  _push(ssrRenderComponent(_component_SvgSprite, {
    symbol: "location-dot",
    size: "0 0 24 24",
    role: "presentation",
    class: "icon"
  }, null, _parent));
  _push(`<span>Chi nhánh trung tâm</span></li><ul class="setting-list | nav-list"><li class="align-center">`);
  _push(ssrRenderComponent(_component_SvgSprite, {
    symbol: "bell",
    size: "0 0 24 24",
    role: "presentation",
    class: "icon"
  }, null, _parent));
  _push(`</li><li class="align-center">`);
  _push(ssrRenderComponent(_component_SvgSprite, {
    symbol: "gear",
    size: "0 0 24 24",
    role: "presentation",
    class: "icon"
  }, null, _parent));
  _push(`</li><li class="align-center">`);
  _push(ssrRenderComponent(_component_SvgSprite, {
    symbol: "user",
    size: "0 0 24 24",
    role: "presentation",
    class: "icon"
  }, null, _parent));
  _push(`</li></ul></ul></nav></div></div></div></section><section class="bottom-nav | padding-block-100"><div class="container"><nav class="nav-wrapper"><ul class="primary-nav | nav-list"><li>Tổng quan</li><li class="room-nav"> Phòng <ul class="sub-menu | padding-block-200"><li><a href="">Hạng phòng &amp; Phòng</a></li><li><a href="">Thiết lập giá</a></li></ul></li><li>Hàng hóa</li><li>Giao dịch</li><li>Đối tác</li><li>Nhân viên</li><li>Số quỹ</li><li>Bán online</li><li>Báo cáo</li></ul><button class="admin-button" data-type="inverted">`);
  _push(ssrRenderComponent(_component_SvgSprite, {
    symbol: "bell-concierge",
    size: "0 0 24 24",
    role: "presentation",
    class: "icon"
  }, null, _parent));
  _push(` Lễ tân </button></nav></div></section></header>`);
}
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Header.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const Header = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["ssrRender", _sfc_ssrRender]]);
const _sfc_main = {
  __name: "Admin",
  __ssrInlineRender: true,
  setup(__props) {
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "admin-layout" }, _attrs))}>`);
      _push(ssrRenderComponent(Header, null, null, _parent));
      _push(`<main>`);
      ssrRenderSlot(_ctx.$slots, "default", {}, null, _push, _parent);
      _push(`</main></div>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Layouts/Admin.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
createServer(
  (page) => createInertiaApp({
    page,
    render: renderToString,
    title: (title) => `Sona Hotel ${title}`,
    resolve: (name) => {
      const pages = /* @__PURE__ */ Object.assign({
        "./Pages/Admin/Room.vue": __vite_glob_0_0,
        "./Pages/Guest/About.vue": __vite_glob_0_1,
        "./Pages/Guest/Home.vue": __vite_glob_0_2
      });
      const page2 = pages[`./Pages/${name}.vue`];
      if (name.startsWith("Admin/")) {
        page2.default.layout = page2.default.layout || _sfc_main;
      } else if (name.startsWith("Guest/")) {
        page2.default.layout = page2.default.layout || _sfc_main$2;
      }
      return page2;
    },
    setup({ App, props, plugin }) {
      const vueApp = createSSRApp({ render: () => h$1(App, props) }).use(plugin).use(_, {
        ...page.props.ziggy,
        location: new URL(page.props.ziggy.location)
      }).component("Head", Head).component("Link", Link).component("Breadcrumb", Breadcrumb).use(svgSpritePlugin, { url: "/icon/sprite-icons.svg" }).use(PrimeVue, {
        theme: {
          preset: Aura,
          options: { darkModeSelector: false }
        }
      });
      vueApp.config.globalProperties.$getBgStyle = (url) => {
        let finalUrl = url;
        if (!finalUrl.startsWith("http")) {
          finalUrl = "/" + finalUrl.replace(/^\/+/, "");
        }
        return { backgroundImage: `url(${finalUrl})` };
      };
      return vueApp;
    }
  }),
  { cluster: true }
);
