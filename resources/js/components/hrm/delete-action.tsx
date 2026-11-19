import { Form } from '@inertiajs/react';
import { Trash2 } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import type { RouteFormDefinition } from '@/wayfinder';

type Props = {
    action: RouteFormDefinition<'post'>;
    title?: string;
    description?: string;
};

export default function DeleteAction({
    action,
    title = 'Delete record',
    description = 'This action cannot be undone. Are you sure you want to continue?',
}: Props) {
    const [open, setOpen] = useState(false);

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogTrigger asChild>
                <Button variant="ghost" size="icon" aria-label={title}>
                    <Trash2 className="text-destructive size-4" />
                </Button>
            </DialogTrigger>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{title}</DialogTitle>
                    <DialogDescription>{description}</DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button
                        type="button"
                        variant="secondary"
                        onClick={() => setOpen(false)}
                    >
                        Cancel
                    </Button>
                    <Form
                        {...action}
                        options={{ preserveScroll: true }}
                        onSuccess={() => setOpen(false)}
                    >
                        {({ processing }) => (
                            <Button
                                type="submit"
                                variant="destructive"
                                disabled={processing}
                            >
                                Delete
                            </Button>
                        )}
                    </Form>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
