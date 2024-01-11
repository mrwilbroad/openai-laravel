import React, { useState } from "react";
import Authenticated from "@/Layouts/AuthenticatedLayout";
import { Formik, ErrorMessage, Form, Field } from "formik";
import ErrorInput from "@/Components/ErrorInput";
import { router } from "@inertiajs/react";
import { WebSocketInstance } from "@/bootstrap";
import { useEffect } from "react";
import { Spinner } from "react-bootstrap";

const dashboard = () => {
    const inValues = {
        message: "",
    };

    const [Output, setOutput] = useState("");
    const [Waiting, setWaiting] = useState({
        loading: false,
        message: "",
    });

    const HandleSubmit = async (values, subprops) => {
        setWaiting({
            ...Waiting,
            loading: true,
        })
        router.visit("/OpenAi/dashboard", {
            method: "post",
            data: values,
            preserveScroll: true,
            preserveState: true,
            onError: (errors) => {
                setWaiting({
                    ...Waiting,
                    loading: false,
                })
                for (const e in errors) {
                    subprops.setFieldError(e, errors[e]);
                }
            },
            onSuccess: ({ props }) => {
                const { flush } = props;
                const { message } = flush;
                setOutput(message);
            },
        });
    };

    useEffect(() => {
        const ChatChannel = WebSocketInstance.channel("UserChatChannel");
        ChatChannel.listen("UserChatEvent", (ev) => {
            setWaiting({
                ...Waiting,
                loading: ev.type != 'complete',
                message: ev.message
            })
            console.log(ev);
        });

        return () => {
            ChatChannel.stopListening("UserChatChannel");
        };
    }, []);

    return (
        <Authenticated>
            <h5>start using chart GPT</h5>

            {Output && <section className="border p-3">{Output}</section>}
            <Formik initialValues={inValues} onSubmit={HandleSubmit}>
                {(formik) => {
                    return (
                        <Form>
                            <section className="col-lg-6 vstack gap-3">
                                <section>
                                    <label htmlFor="" className="form-label">
                                        Send Question
                                    </label>
                                    <Field
                                        name="message"
                                        as="textarea"
                                        className="form-control"
                                    />
                                    <ErrorMessage
                                        name="message"
                                        component={ErrorInput}
                                    />
                                </section>

                                <button
                                    disabled={Waiting.loading}
                                    type="submit"
                                    className="btn btn-success col-lg-5 col-md-4 col-sm-5"
                                >
                                    {Waiting.loading
                                        ? "please wait ..."
                                        : "Send message"}
                                    {Waiting.loading && (
                                        <Spinner
                                            className="mx-2"
                                            animation="border"
                                            variant="light"
                                            role="status"
                                            size="sm"
                                        />
                                    )}
                                </button>
                            </section>
                        </Form>
                    );
                }}
            </Formik>

            {Waiting.message && (
                <section className="vstack gap-3 my-3">
                    <section>
                        <p className="text-success m-0 p-0 d-inline pe-2">
                            Response :
                        </p>
                        <p className="m-0 p-0 d-inline">{Waiting.message}</p>
                    </section>
                </section>
            )}
        </Authenticated>
    );
};

export default dashboard;
