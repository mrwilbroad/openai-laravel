import React, { useState } from "react";
import Authenticated from "@/Layouts/AuthenticatedLayout";
import { Formik, ErrorMessage, Form, Field } from "formik";
import ErrorInput from "@/Components/ErrorInput";
import { router } from "@inertiajs/react";

const dashboard = () => {
    const inValues = {
        content: "",
    };

    const [Output, setOutput] = useState("");
    const HandleSubmit = async (values, subprops) => {
        subprops.setSubmitting(false);
        router.visit("/OpenAi/dashboard", {
            method: "post",
            data: values,
            preserveScroll: true,
            preserveState: true,
            onSuccess: ({props}) => {
              const { flush } = props;
              const { message } = flush;
              setOutput(message);
            },
        });
    };

    return (
        <Authenticated>
            <h5>start using chart GPT</h5>

           {Output && <section className="border p-3">{Output}</section>}
            <Formik initialValues={inValues} onSubmit={HandleSubmit}>
                {(formik) => {
                    return (
                        <Form>
                            <section className="col-5 vstack gap-3">
                                <section>
                                    <label htmlFor="" className="form-label">
                                        Send Question
                                    </label>
                                    <Field
                                        name="content"
                                        as="textarea"
                                        className="form-control"
                                    />
                                    <ErrorMessage
                                        name="content"
                                        component={ErrorInput}
                                    />
                                </section>

                                <section>
                                    <button
                                        type="submit"
                                        className="btn btn-success"
                                    >
                                        send
                                    </button>
                                </section>
                            </section>
                        </Form>
                    );
                }}
            </Formik>
        </Authenticated>
    );
};

export default dashboard;
